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
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddInvitationController extends AbstractController
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
     * @throws \Exception
     */
    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $civility = $content["civility"] ?? null;
        $name = $content["name"] ?? null;
        $phoneNumber = $content["phoneNumber"] ?? null;
        $count = $content["count"] ?? null;
        $eventId = $content["eventId"] ?? null;
        $tableId = $content["tableId"] ?? null;

        $event = $this->eventRepository->find($eventId);

        if ($this->invitationRepository->invitationExists($phoneNumber, $event)) {
            $invitations = $this->invitationRepository->findByEvent($event);
            $response = [
                'success'   => false,
                'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
                'message'   => "Un invité est déjà enrégistré avec ce numéro de téléphone",
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        }

        do {
            $code = $this->OTPService->generate(6);
        } while ($this->invitationRepository->invitationCodeExists($code));

        $invitation = (new Invitation())
            ->setName($name)
            ->setPhoneNumber($phoneNumber)
            ->setEvent($event)
            ->setCivility($civility)
            ->setStatus("created")
            ->setCount($count)
            ->setCode($code)
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
            ->labelText($code)
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();

        $result->saveToFile($destinationPath . "/" . $fileName);

        $message = "Bonjour " . $invitation->getCivility() . " " . $invitation->getName();
        $message .= ",\nVous êtes invité à l'évenement \"{$invitation->getEvent()->getCategory()->getName()} - {$invitation->getEvent()->getName()}\" le " . $this->dateService->getFullDate($invitation->getEvent()->getDate(), true, true)  . " à {$invitation->getEvent()->getDate()->format('h:i')}.";
        $message .= "\n\nPour accéder à votre invitation, veuillez ouvrir le lien suivant.";
        $message .= "\nhttps://kuuwaa.com/invitations/" . $invitation->getCode();
        $message .= "\n\nSi vous rencontrez des difficultés pour ouvrir le lien ou pour accéder à l'application KuuWaa, veuillez retenir le code d'invitation à 6 chiffres ({$invitation->getCode()}) et présentez-le lors de la vérification à l'entrée de l'événement.";

        $body = "$message\n\nCeci est un message automatique, veuillez ne pas y repondre.";

        $invitation->setCodePath("/events/invitations/qr_code/" . $fileName);
        $invitation->setMessage($body);

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