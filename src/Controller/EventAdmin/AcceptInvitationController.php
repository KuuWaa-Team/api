<?php

namespace App\Controller\EventAdmin;

use App\Entity\Event\EventAdmin;
use App\Repository\Event\EventAdminRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventAdminRepository $adminRepository,
        private readonly UserRepository $userRepository,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->json([
                'success' => false,
                'message' => 'Requête JSON invalide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $code = $content['invitationCode'] ?? null;
        $userId = $content['userId'] ?? null;

        if (!$code || !$userId) {
            return $this->json([
                'success' => false,
                'message' => 'Le code d\'invitation et l\'identifiant utilisateur sont requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ], Response::HTTP_NOT_FOUND);
        }

        $eventAdmin = $this->adminRepository->findOneBy(['invitationCode' => $code]);
        if (!$eventAdmin) {
            return $this->json([
                'success' => false,
                'message' => 'Invitation introuvable.'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($eventAdmin->getStatus() === 'accepted') {
            return $this->json([
                'success' => false,
                'message' => 'Cette invitation a déjà été acceptée.'
            ], Response::HTTP_CONFLICT);
        }

        $eventAdmin->setUser($user);
        $eventAdmin->setStatus('accepted');

        $this->adminRepository->save($eventAdmin, true);

        return $this->json([
            'success' => true,
            'message' => 'Invitation acceptée avec succès.'
        ], Response::HTTP_OK);
    }
}
