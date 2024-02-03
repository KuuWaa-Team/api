<?php

namespace App\Controller\Ticket;

use App\Entity\Invitation;
use App\Entity\Ticket;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use App\Repository\TicketRepository;
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

class AddTicketController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TableRepository $tableRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly OTPService $OTPService,
        private readonly DateService $dateService
    ) {
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function __invoke(Request $request, KernelInterface $kernel)
    // : JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $code = $content["code"] ?? null;
        $status = $content["status"] ?? null;
        $eventId = $content["eventId"] ?? null;
        $event = $this->eventRepository->find($eventId);



        do {
            $code = $this->OTPService->generate(6);
        } while ($this->ticketRepository->ticketCodeExists($code));

        $ticket = (new Ticket())
            ->setCode($code)
            ->setEvent($event)
            ->setStatus("created");



        $this->ticketRepository->save($ticket, true);

        $qrCodeData = [
            'type' => 'KuuWaa',
            'id' => $ticket->getId(),
            'status' => $ticket->getStatus(),
            'kwEv' => "KW000" . $event->getId()
        ];

        $jsonQrCodeData = json_encode($qrCodeData, JSON_THROW_ON_ERROR);
        $fileName = uniqid('', true) . '.png';
        $destinationPath = $kernel->getProjectDir() . '/public/events/tickets/qr_code';

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

        $ticket->setCodePath("/events/tickets/qr_code/" . $fileName);

        $this->ticketRepository->save($ticket, true);

        $response = [
            'success'   => true,
            'invitations' => $this->normalizer->normalize($ticket, null, ['groups' => ['ticket:pRead']]),
            'message'   => "Ticket enrégistré avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
