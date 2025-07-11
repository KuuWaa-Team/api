<?php

namespace App\Controller\Event\Game;

use App\Entity\Event\GameAssertion;
use App\Entity\Event\GameQuestion;
use App\Repository\Event\GameAssertionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AddAssertionToQuestion extends AbstractController
{
    public function __construct(
        private readonly GameAssertionRepository $assertionRepository
    ) {}

    /**
     * @throws \JsonException
     */
    public function __invoke(GameQuestion $question, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $assertion = (new GameAssertion())
            ->setQuestion($question)
            ->setIsCorrect(false)
            ->setText($data['text']);
        $this->assertionRepository->save($assertion);

        return new JsonResponse([
            'success' => true,
            'message' => 'Assertion ajoutée avec succès',
            'data' => [
                'id' => $assertion->getId(),
                'text' => $assertion->getText()
            ],
            'question_id' => $question->getId()
        ], 201);
    }
}