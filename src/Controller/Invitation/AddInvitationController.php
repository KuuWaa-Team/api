<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TableRepository $tableRepository,
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $success = true;
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $civility = $content["civility"] ?? null;
        $name = $content["name"] ?? null;
        $phoneNumber = $content["phoneNumber"] ?? null;
        $count = $content["count"] ?? null;
        $eventId = $content["eventId"] ?? null;
        $tableId = $content["tableId"] ?? null;

        $event = $this->eventRepository->find($eventId);

        if ($this->invitationRepository->InvitationExists($phoneNumber)) {
            $invitations = $this->invitationRepository->findByEvent($event);
            $response = [
                'success'   => false,
                'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
                'message'   => "Un invité est déjà enrégistré avec ce numéro de téléphone",
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        }

        $invitation = (new Invitation())
            ->setName($name)
            ->setPhoneNumber($phoneNumber)
            ->setEvent($event)
            ->setCivility($civility)
            ->setStatus("created")
            ->setCount($count)
        ;

        if ($tableId !== null) {
            $table = $this->tableRepository->find($tableId);
            $countTable = $table->getCount() ?? 0;
            $countTableTaken = $table->getCountTaken() ?? 0;

            if ($countTable < $countTableTaken + $count) {
                $response = [
                    'success'   => false,
                    'message'   => "Pas assez de place à la table {$table->getName()} pour cette invitation",
                ];

                return new JsonResponse($response, Response::HTTP_OK);
            }

            $invitation->setInvitationTable($table);
            $table->setCountTaken($countTableTaken + $count);
            $this->tableRepository->save($table, true);
        }

        $this->invitationRepository->save($invitation, true);

        $qrCodeData = [
            'status' => $invitation->getStatus(),
            'civility' => $invitation->getCivility(),
            'name' => $invitation->getName(),
            'count' => $invitation->getCount(),
            'table' => $invitation->getInvitationTable()->getName(),
        ];

        $jsonQrCodeData = json_encode($qrCodeData, JSON_THROW_ON_ERROR);
//        $qrCode = new QrCode($jsonQrCodeData);
//        $qrCode->setSize(300);
        $fileName = uniqid('', true) . '.png';
        $destinationPath = $kernel->getProjectDir() . '/public/events/invitations/qr_code';
//        $qrCode->render($destinationPath);
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($jsonQrCodeData)
            ->encoding(new Encoding('UTF-8'))
//            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->logoPath($kernel->getProjectDir() . '/public/images/logo-dark.png')
            ->logoResizeToWidth(80)
//            ->logoPunchoutBackground(false)
            ->validateResult(false)
            ->build();

        $result->saveToFile($destinationPath . "/" . $fileName);

        $invitation->setCodePath($fileName);

        $this->invitationRepository->save($invitation, true);

        $eventInvitationsCount = $event->getInvitationsCount() ?? 0;

        $event->setInvitationsCount($eventInvitationsCount + 1);
        $this->eventRepository->save($event, true);

        $invitations = $this->invitationRepository->findByEvent($invitation->getEvent());
        $response = [
            'success'   => true,
            'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
            'message'   => "Invitation enrégistrée avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}