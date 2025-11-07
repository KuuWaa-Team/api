<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Entity\Table;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use App\Service\ApiService;
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
        private readonly ApiService $apiService,
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
        $apiUrl = $this->apiService->getApiUrl();

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
            'kwEv' => "KW000" . $event->getId(),
            'code' => $invitation->getCode() // add invitation code
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
            ->labelText($invitation->getFullname())
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();

        $result->saveToFile($destinationPath . "/" . $fileName);
        $invitation->setCodePath("/events/invitations/qr_code/" . $fileName);

        $invitMessage  = "Bonjour " . $civility . " " . $name . ",\n\n";
        $invitMessage .= "Vous êtes cordialement invité à l’événement « " . $event->getCategory()->getName() . " - " . $event->getTitle() . " », qui se tiendra le ";
        $invitMessage .= $this->dateService->getFullDate($event->getStartDate(), true, true) . " à " . $event->getStartDate()->format('H:i') . ".\n\n";

        $invitMessage .= "🎟️ Pour accéder à votre invitation personnalisée, veuillez cliquer sur le lien ci-dessous :\n";
        $invitMessage .= $apiUrl . "/invitations/" . $invitation->getSlug() . "\n\n";

        $invitMessage .= "🔐 En cas de difficulté pour ouvrir le lien, veuillez conserver le code d'invitation à 6 chiffres suivant : ";
        $invitMessage .= $invitation->getCode() . ". Il vous sera demandé lors du contrôle à l’entrée de l’événement.\n\n";

        $invitMessage .= "Au plaisir de vous compter parmi nous pour célébrer cet instant unique.\n\n";
        $invitMessage .= $event->getTitle(); 

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