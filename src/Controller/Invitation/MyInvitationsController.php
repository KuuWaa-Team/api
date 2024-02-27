<?php

namespace App\Controller\Invitation;

use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MyInvitationsController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
        private readonly  NormalizerInterface $normalizer
    )
    {
    }

    public function __invoke(UserInterface $user)
    {
        $phone = $user->getPhoneNumber();
        $invitations = $this->invitationRepository->findByPhoneNumberAndStatus($phone, 'sent');

        $response = [
            'success'   => true,
            'data' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:fRead']])
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }
}