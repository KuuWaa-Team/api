<?php

namespace App\Controller\Ticket;

use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MyTicketsController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
        private readonly  NormalizerInterface $normalizer
    ) {
    }

    public function __invoke(UserInterface $user)
    {
        $phone = $user->getPhoneNumber();
        $invitations = $this->invitationRepository->findByPhoneNumber($phone);

        $response = [
            'success'   => true,
            'data' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:fRead']])
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
