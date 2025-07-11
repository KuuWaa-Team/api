<?php

namespace App\Controller\Event\Game;

use App\Entity\Event\GameQuestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EditGameQuestion extends AbstractController
{
    public function __invoke(GameQuestion $gameQuestion, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['title'])) {
            return new JsonResponse(['success' => false, 'message' => 'Title manquant'], 400);
        }

        $gameQuestion->setTitle($data['title']);

        $em->persist($gameQuestion);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Question mise à jour avec succès',
            'question_id' => $gameQuestion->getId(),
        ]);
    }
}
