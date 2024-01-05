<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListingEventInvitationsController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly NormalizerInterface $normalizerInterface,
    ) {
    }

    public function __invoke(Event $event, UserInterface $user): JsonResponse
    {
        $success = true;


        $response = [
            'success'   => $success,
            'data' => $this->normalizerInterface->normalize($event->getInvitations(), null, ['groups' => ['read:event:invitations']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
