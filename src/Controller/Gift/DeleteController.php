<?php

namespace App\Controller\Gift;

use App\Entity\Event\Gift;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
    }

    public function __invoke(Gift $gift): JsonResponse
    {
        $this->em->remove($gift);
        $this->em->flush();

        $response = [
            'success' => true,
            'message' => "Cadeau supprimé avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}