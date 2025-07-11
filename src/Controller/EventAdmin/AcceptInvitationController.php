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
            ], Response::HTTP_OK);
        }

        $code = $content['invitationCode'] ?? null;
        $userId = $content['userId'] ?? null;

        if (!$code || !$userId) {
            return $this->json([
                'success' => false,
                'message' => 'Le code d\'invitation et l\'identifiant utilisateur sont requis.'
            ], Response::HTTP_OK);
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ], Response::HTTP_OK);
        }

        $eventAdmin = $this->adminRepository->findOneBy(['invitationCode' => $code]);
        if (!$eventAdmin) {
            return $this->json([
                'success' => false,
                'message' => 'Invitation introuvable.'
            ], Response::HTTP_OK);
        }

        if ($eventAdmin->getUser() !== null) {
            return $this->json([
                'success' => false,
                'message' => 'Invitation expirée.'
            ], Response::HTTP_OK);
        }

        if ($user->getId() === $eventAdmin->getEvent()?->getUser()?->getId()) {
            return $this->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas accepter une invitation à votre propre évènement.'
            ], Response::HTTP_OK);
        }

        if ($eventAdmin->getStatus() === 'accepted') {
            return $this->json([
                'success' => false,
                'message' => 'Cette invitation a déjà été acceptée.'
            ], Response::HTTP_OK);
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
