<?php

namespace App\Controller\Event\Game;

use App\Entity\Event;
use App\Entity\Event\GameAssertion;
use App\Entity\Event\GameQuestion;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameQuestionController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $em,
    )
    {}

    public function __invoke(
        Request $request,
        Event $event
    ): JsonResponse {
        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['title']) || !isset($data['assertions']) || !is_array($data['assertions'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        $question = new GameQuestion();
        $question->setTitle($data['title']);
        $question->setPosition($data['position'] ?? null);
        $question->setEvent($event);

        foreach ($data['assertions'] as $a) {
            if (!isset($a['text'])) {
                continue;
            }

            $assertion = new GameAssertion();
            $assertion->setText($a['text']);
            $assertion->setIsCorrect($a['isCorrect'] ?? false);
            $assertion->setQuestion($question);

            $this->em->persist($assertion);
        }

        $this->em->persist($question);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Question ajoutée avec succès',
            'question_id' => $question->getId()
        ], 201);
    }
}
