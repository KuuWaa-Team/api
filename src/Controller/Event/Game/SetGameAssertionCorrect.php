<?php

namespace App\Controller\Event\Game;

use App\Entity\Event\GameAssertion;
use App\Repository\Event\GameAssertionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SetGameAssertionCorrect extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GameAssertionRepository $assertionRepository,
    ) {
    }

    public function __invoke(GameAssertion $assertion): JsonResponse
    {
        $question = $assertion->getQuestion();
        foreach ($question?->getGameAssertions() as $item) {
            $item->setIsCorrect(false);
            $this->assertionRepository->save($item);
        }
        $assertion->setIsCorrect(true);
        $this->assertionRepository->save($assertion);
        $this->em->flush();
        $response = [
            'success'   => true,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}