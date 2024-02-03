<?php

namespace App\Controller\QrCode;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class GenerateController extends AbstractController
{
    #[Route('/qr/code/generate', name: 'app_qr_code_generate', methods: Request::METHOD_POST)]
    public function index(KernelInterface $kernel): Response
    {
        $websiteUrl = 'https://kuuwaa.com';
        $fileName = uniqid('', true) . '.png';
        $destinationPath = $kernel->getProjectDir() . '/public/events/invitations/qr_code/kuuwaa';

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($websiteUrl)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->logoPath($kernel->getProjectDir() . '/public/images/logo-dark.png')
            ->logoResizeToWidth(80)
            ->validateResult(false)
            ->build();

        $result->saveToFile($destinationPath . "/" . $fileName);

        return [
            "status" => "ok"
        ];
    }
}
