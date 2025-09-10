<?php

namespace App\Controller\Admin;

use App\Repository\DownloadRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DownloadRequestController extends AbstractController
{
    public function __construct(
        private readonly DownloadRequestRepository $downloadRequestRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/download/request', name: 'app_admin_download_request')]
    public function index(): Response
    {
        $requests = $this->downloadRequestRepository->findAll();
        $normalizedData = $this->normalizer->normalize(
            $requests,
            null,
            ['groups' => ['request:fRead']]
        );

        return $this->json([
            'success' => true,
            'data' => $normalizedData
        ]);
    }
}
