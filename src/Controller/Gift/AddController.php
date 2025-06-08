<?php

namespace App\Controller\Gift;

use App\Entity\Event;
use App\Repository\Event\BookRepository;
use App\Repository\Event\GiftRepository;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddController extends AbstractController
{
    public function __construct(
        private readonly GiftRepository $giftRepository,
    ){}

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $name = $content['name'];

        $gift = (new Event\Gift())
            ->setName($name)
            ->setEvent($event);
        $this->giftRepository->save($gift);

        $response = [
            'success'   => true,
            'message'   => "Cadeau ajouté avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}