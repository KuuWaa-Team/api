<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListingEventInvitationsController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizerInterface,
        private readonly InvitationRepository $invitationRepository
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Event $event): JsonResponse
    {
        $invitations = $this->invitationRepository->findByEvent($event);

        $response = [
            'success' => true,
            'data'    => $this->normalizerInterface->normalize($invitations, null, ['groups' => ['invitation:pRead']])
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
