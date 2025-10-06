<?php

namespace App\Controller;

use App\Entity\NewsLetter;
use App\Service\AdminService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class NewsLetterController extends AbstractController
{
    #[Route('/api/news-letter/new', name: 'app_news_letter', methods: ['POST'])]
    public function index(Request $request, AdminService $adminService, EntityManagerInterface $em): JsonResponse
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

        // Vérifier si l'adresse existe déjà
        $existing = $em->getRepository(NewsLetter::class)->findOneBy(['address' => $email]);

        if ($existing) {
            return $this->json([
                'success' => false,
                'message' => 'Cette adresse est déjà inscrite.'
            ], 409);
        }

        // Créer une nouvelle inscription
        $newsLetter = new NewsLetter();
        $newsLetter->setAddress($email);
        $newsLetter->setCreateAt(new \DateTimeImmutable());
        $newsLetter->setIsActif(true);

        $em->persist($newsLetter);
        $em->flush();

        $adminService->addUnreadNewNewsletter();

        return $this->json([
            'success' => true,
            'message' => 'Inscription réussie.',
            'data' => [
                'id' => $newsLetter->getId(),
                'address' => $newsLetter->getAddress(),
                'createAt' => $newsLetter->getCreateAt()->format('Y-m-d H:i:s'),
                'isActif' => $newsLetter->isActif(),
            ]
        ], 201);
    }
}
