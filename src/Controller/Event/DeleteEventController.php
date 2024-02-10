<?php

namespace App\Controller\Event;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteEventController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {}

    public function __invoke(Event $event): JsonResponse
    {
        $this->em->remove($event);
        $this->em->flush();

        $response = [
            'success'   => true,
            'message'   => "Evenement supprimé avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}