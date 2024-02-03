<?php

namespace App\Controller\Invitation;

use App\Repository\InvitationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CheckByCodeController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer,
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

        if ($invitation === null) {
            $message = "Code invalide";
        } else if ($invitation->getStatus() === 'verified') {
            $message = "Cette invitation à été déjà vérifiée";
        } else {
            $success = true;
            $data = $this->normalizer->normalize($invitation, null, ['groups' => ['invitation:fRead']]);
            $message = "Invitation valide. L'invité peut accéder à la salle";
        }

        $response = [
            'success' => $success,
            'message' => $message,
            "data"    => $data
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}