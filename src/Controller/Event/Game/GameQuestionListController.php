<?php

namespace App\Controller\Event\Game;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GameQuestionListController extends AbstractController
{
    #[Route('/api/event/{id}/questions', name: 'list_game_questions', methods: ['GET'])]
    public function __invoke(Event $event): JsonResponse
    {
        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], 404);
        }

        $questions = $event->getQuestions();

        $response = [];

        foreach ($questions as $q) {
            $assertions = [];

            foreach ($q->getGameAssertions() as $a) {
                $assertions[] = [
                    'id' => $a->getId(),
                    'text' => $a->getText(),
                    'isCorrect' => $a->isCorrect(),
                ];
            }

            $response[] = [
                'id' => $q->getId(),
                'title' => $q->getTitle(),
                'position' => $q->getPosition(),
                'assertions' => $assertions,
            ];
        }

        return new JsonResponse($response);
    }
}
