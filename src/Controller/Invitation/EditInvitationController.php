<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use App\Service\DateService;
use App\Service\OTPService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EditInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TableRepository $tableRepository,
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly OTPService $OTPService,
        private readonly DateService $dateService
    )
    {
    }
    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Invitation $invitation, KernelInterface $kernel)
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $phoneNumber = $content["phoneNumber"] ?? null;
        $tableId = $content["tableId"] ?? null;

        $event = $invitation->getEvent();

        if ($phoneNumber !== null && $phoneNumber !== $invitation->getPhoneNumber()) {
            if ($this->invitationRepository->invitationExists($phoneNumber, $event)) {
                $invitations = $this->invitationRepository->findByEvent($event);
                $response = [
                    'success'   => false,
                    'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
                    'message'   => "Un autre invité est déjà enrégistré avec ce numéro de téléphone",
                ];

                return new JsonResponse($response, Response::HTTP_OK);
            }

            $invitation->setPhoneNumber($phoneNumber);
        }

        if ($tableId !== null && ($invitation->getInvitationTable() === null || $tableId !== $invitation->getInvitationTable()->getId())) {
            $oldTable = $invitation->getInvitationTable();
            $newTable = $this->tableRepository->find($tableId);

            $countNewTable = $newTable->getCount() ?? 0;

            if ($oldTable !== null) {
                $countOldTableTaken = $oldTable->getCountTaken() ?? 0;
            }
            $countNewTableTaken = $newTable->getCountTaken() ?? 0;

            if ($countNewTable < $countNewTableTaken + $invitation->getCount()) {
                $response = [
                    'success'   => false,
                    'message'   => "Pas assez de place à la table {$newTable->getName()} pour cette invitation",
                ];
                return new JsonResponse($response, Response::HTTP_OK);
            }
            $invitation->setInvitationTable($newTable);
            $newTable->setCountTaken($countNewTableTaken + $invitation->getCount());
            if ($oldTable !== null) {
                $oldTable->setCountTaken($countOldTableTaken - $invitation->getCount());
                $this->tableRepository->save($oldTable, true);
            }
            $this->tableRepository->save($newTable, true);

            $qrCodeData = [
                'type' => 'KuuWaa',
                'id' => $invitation->getId(),
                'status' => $invitation->getStatus(),
                'civility' => $invitation->getCivility(),
                'name' => $invitation->getName(),
                'count' => $invitation->getCount(),
                'table' => $invitation->getInvitationTable()?->getName(),
                'kwEv' => "KW000" . $event->getId()
            ];

            $jsonQrCodeData = json_encode($qrCodeData, JSON_THROW_ON_ERROR);
            $fileName = uniqid('', true) . '.png';
            $destinationPath = $kernel->getProjectDir() . '/public/events/invitations/qr_code';

            $result = Builder::create()
                ->writer(new PngWriter())
                ->writerOptions([])
                ->data($jsonQrCodeData)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->logoPath($kernel->getProjectDir() . '/public/images/logo-dark.png')
                ->logoResizeToWidth(80)
                ->labelText($invitation->getCode())
                ->labelFont(new NotoSans(20))
                ->labelAlignment(LabelAlignment::Center)
                ->validateResult(false)
                ->build();

            $result->saveToFile($destinationPath . "/" . $fileName);
            $invitation->setCodePath("/events/invitations/qr_code/" . $fileName);
            $this->invitationRepository->save($invitation, true);
        }

        $this->invitationRepository->save($invitation, true);

        $invitations = $this->invitationRepository->findByEvent($invitation->getEvent());
        $response = [
            'success'   => true,
            'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
            'message'   => "Invitation modifiée avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}