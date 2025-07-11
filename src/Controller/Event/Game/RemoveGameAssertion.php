<?php

namespace App\Controller\Event\Game;

use App\Entity\Event\GameAssertion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RemoveGameAssertion extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(GameAssertion $assertion)
    {
        $this->em->remove($assertion);
        $this->em->flush();
        $response = [
            'success'   => true,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}