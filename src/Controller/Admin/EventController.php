<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/event', name: 'app_admin_event', methods: ['GET'])]
    public function index(): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException("Veuillez vous connecter", 1);
        }

        $user->setCountNewEvents(0);
        $this->userRepository->save($user);
        
        $events = $this->eventRepository->findAll();

        $normalizedData = $this->normalizer->normalize(
            $events,
            null,
            ['groups' => ['event:fRead']]
        );

        return $this->json([
            'success' => true,
            'data' => $normalizedData
        ]);
    }

    #[Route('/admin/event/{id}', name: 'app_admin_event_detail', methods: ['GET'])]
    public function detail(Event $event): JsonResponse
    {
        $normalizedDataEvents = $this->normalizer->normalize(
            $event,
            null,
            ['groups' => ['event:fRead']]
        );
        $invitations = $event->getInvitations();
        $normalizedDataInvitations = $this->normalizer->normalize(
            $invitations,
            null,
            ['groups' => ['event:fRead']]
        );

        return $this->json([
            'success' => true,
            'data' => [
                'event' => $normalizedDataEvents,
                'invitations' => $normalizedDataInvitations
            ]
        ]);
    }

}
