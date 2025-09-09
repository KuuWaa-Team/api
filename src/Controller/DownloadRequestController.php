<?php

namespace App\Controller;

use App\Entity\DownloadRequest;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DownloadRequestController extends AbstractController
{
    #[Route('/api/downlaod/request', name: 'app_download_request', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $em, SmsService $smsService): JsonResponse
    {
        // Décoder le JSON envoyé
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['address'])) {
            return $this->json([
                'success' => false,
                'message' => 'Adresse email requise.'
            ], 400);
        }

        $email = trim($data['address']);

        // Vérifier si l'adresse est valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'success' => false,
                'message' => 'Adresse email invalide.'
            ], 400);
        }

        // Créer une nouvelle demande
        $downloadRequest = new DownloadRequest();
        $downloadRequest->setAddress($email);
        $downloadRequest->setCreatedAt(new \DateTimeImmutable());
        $downloadRequest->setSent(false);

        $em->persist($downloadRequest);
        $em->flush();

        $smsService->send('243814063056', "Nouvelle demande de telechargement de l'app Android : " . $email);

        return $this->json([
            'success' => true,
            'message' => 'Inscription réussie.'
        ], 201);
    }
}
