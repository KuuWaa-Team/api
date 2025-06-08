<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PresenceConfirmationController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
    )
    {}

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function __invoke(Request $request, Invitation $invitation): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $presence = $content["presence"] ?? false;

        $invitation->setWillBePresent($presence);
        $this->invitationRepository->save($invitation);

        return new JsonResponse(["success" => true], Response::HTTP_OK);
    }
}