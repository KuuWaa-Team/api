<?php

namespace App\Controller\Event\Game;

use App\Entity\Event\GameQuestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeleteGameQuestionController extends AbstractController
{
    public function __invoke(GameQuestion $gameQuestion, EntityManagerInterface $em): JsonResponse
    {
        foreach ($gameQuestion->getGameAssertions() as $assertion) {
            $em->remove($assertion);
        }

        $em->remove($gameQuestion);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Question et assertions supprimées avec succès.'
        ]);
    }
}
