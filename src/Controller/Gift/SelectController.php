<?php


namespace App\Controller\Gift;

use App\Entity\Event\Gift;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SelectController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Gift $gift): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $invitationId = $content['invitationId'];
        $invitation = $this->invitationRepository->find($invitationId);

        $invitation->addGift($gift);
        $this->invitationRepository->save($invitation);

        $response = [
            'success'   => true,
            'message'   => "Cadeau ajouté avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
