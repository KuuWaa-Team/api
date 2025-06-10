<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Entity\Table;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use App\Service\DateService;
use App\Service\OTPService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
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
        private readonly DateService $dateService,
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
        $count = $content["count"] ?? null;
        $eventId = $content["eventId"] ?? null;
        $tableId = $content["tableId"] ?? null;

        $event = $this->eventRepository->find($eventId);
        do {
            $code = $this->OTPService->generate(6);
        } while ($this->invitationRepository->invitationCodeExists($code));

        $invitation = (new Invitation())
            ->setName($name)
            ->setEvent($event)
            ->setCivility($civility)
            ->setStatus("created")
            ->setCount($count)
            ->setCode($code)
            ->setSlug()
        ;

        if ($tableId !== null) {
            $table = $this->tableRepository->find($tableId);
            if ($table instanceof Table) {
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

        $primaryColor = $event->getTemplate()->getPrimaryColor(); // e.g., "#4285f4"
        list($r, $g, $b) = $this->hexToRgb($primaryColor);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($jsonQrCodeData)
            ->backgroundColor(new Color(255, 255, 255))
            ->foregroundColor(new Color($r, $g, $b))
            ->labelTextColor(new Color($r, $g, $b))
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
        $invitation->setCodePath("/events/invitations/qr_code/" . $fileName);

        $invitMessage = "Bonjour " . $civility . " " . $name;
        $invitMessage .= ",\nVous êtes invité à l'évenement \"{$event->getCategory()->getName()} - {$event->getTitle()}\" le " . $this->dateService->getFullDate($event->getStartDate(), true, true)  . " à {$event->getStartDate()->format('H:i')}.";
        $invitMessage .= "\n\nPour accéder à votre invitation, veuillez ouvrir le lien suivant.";
        $invitMessage .= "\nhttps://kuuwaa.com/invitations/" . $invitation->getSlug();
        $invitMessage .= "\n\nSi vous rencontrez des difficultés pour ouvrir le lien, veuillez retenir le code d'invitation à 6 chiffres ({$invitation->getCode()}) et présentez-le lors de la vérification à l'entrée de l'évenement.";

        $invitation->setMessage($invitMessage);

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

    function hexToRgb(string $hex): array {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0] . $hex[1].$hex[1] . $hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }
}