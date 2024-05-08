<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Repository\InvitationRepository;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ValidateController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
    )
    {
    }

    public function __invoke(Invitation $invitation, KernelInterface $kernel): JsonResponse
    {
        $invitation->setStatus('verified');
        $qrCodeData = [
            'type' => 'KuuWaa',
            'id' => $invitation->getId(),
            'status' => 'verified',
            'civility' => $invitation->getCivility(),
            'name' => $invitation->getName(),
            'count' => $invitation->getCount(),
            'table' => $invitation->getInvitationTable()?->getName(),
            'kwEv' => "KW000" . $invitation->getEvent()->getId()
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

        $response = [
            'success'   => true,
            'message'   => "Invitation validé avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}