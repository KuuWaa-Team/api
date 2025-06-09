<?php

namespace App\Controller\Book;

use App\Entity\Event;
use App\Repository\Event\BookRepository;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
        private readonly BookRepository $bookRepository
    ){}

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = $content['message'];
        $isPublic = $content['public'] ?? false;
        $invitationId = $content['invitationId'];
        $invitation = $this->invitationRepository->find($invitationId);

        $book = (new  Event\Book())
            ->setEvent($event)
            ->setInvitation($invitation)
            ->setMessage($message)
            ->setPublic($isPublic);
        ;
        $this->bookRepository->save($book, true);

        $invitation->setBookSent(true);
        $this->invitationRepository->save($invitation, true);

        $response = [
            'success'   => true,
            'message'   => "Message envoyé avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}