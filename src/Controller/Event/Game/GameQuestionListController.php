<?php

namespace App\Controller\Event\Game;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GameQuestionListController extends AbstractController
{
    public function __invoke(Event $event): JsonResponse
    {
        $questions = $event->getQuestions();
        $response = [
            'success' => true,
            'data' => []
        ];

        foreach ($questions as $q) {
            $assertions = [];

            foreach ($q->getGameAssertions() as $a) {
                $assertions[] = [
                    'id' => $a->getId(),
                    'text' => $a->getText(),
                    'isCorrect' => $a->isCorrect(),
                ];
            }

            $response['data'][] = [
                'id' => $q->getId(),
                'title' => $q->getTitle(),
                'position' => $q->getPosition(),
                'assertions' => $assertions,
            ];
        }

        return new JsonResponse($response);
    }
}
