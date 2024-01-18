<?php

namespace App\Controller\Invitation;

use App\Repository\InvitationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CheckByCodeController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository
    )
    {
    }

    /**
     * @throws ExceptionInterface
     * @throws \JsonException
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $code = $content["code"] ?? null;

        $success = false;
        $data = null;

        $invitation = $this->invitationRepository->findByCode($code);

        $message = null;

        if ($invitation === null) {
            $message = "Code invalide";
        } else if ($invitation->getStatus() === 'verified') {
            $message = "Cette invitation à été déjà vérifiée";
        }

        if ($invitation !== null && $invitation->getStatus() !== 'verified') {
            $success = true;
            $data = $invitation;
        }

        $response = [
            'success' => $success,
            'message' => $message,
            "data"    => $data
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}