<?php

namespace App\Controller\Book;

use App\Entity\Event\Book;
use App\Repository\Event\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function __invoke(Book $book): JsonResponse
    {
        $this->em->remove($book);
        $this->em->flush();

        $response = [
            'success'   => true,
            'message'   => "Message supprimé avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}