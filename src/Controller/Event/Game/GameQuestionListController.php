<?php

namespace App\Controller\Event\Game;

use App\Entity\Event;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GameQuestionListController extends AbstractController
{
    public function __construct (
        private readonly InvitationRepository $invitationRepository
    ) {}
    
    public function __invoke(Event $event): JsonResponse
    {
        $questions = $event->getQuestions();
        $response = [
            'success' => true,
            'data' => [],
            'results' => []
        ];
        
        $playedInvitations = $this->invitationRepository->findBy(
            [
                'event' => $event,
                'quizAnswered' => true
            ],
            [
                'quizPercentage' => 'DESC', 
                'quizAnsweredAt' => 'ASC'   
            ]
        );

        foreach ($playedInvitations as $invitation) {
            $response['results'][] = [
                'name' => $invitation->getCivility() . ' ' . $invitation->getName(),
                'percentage' => $invitation->getQuizPercentage(),
                'date' => $invitation->getQuizAnsweredAt()
            ];
        }

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
