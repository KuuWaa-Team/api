<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Table;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AddTableEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TableRepository $tableRepository,
    ) {
    }

    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = 'Table ajoutée avec succès !';
        $success = true;
        $errorType = null;

        if (array_key_exists('name', $content) && array_key_exists('count', $content) && $content['name'] !== NULL && $content['count'] !== NULL) {
            $name = $content['name'];
            $count = $content['count'];
            $table = (new Table())
                ->setName($name)
                ->setCount($count);
            $event->addTable($table);
            $this->tableRepository->save($table, false);
            $this->eventRepository->save($event, true);
        } else {
            $success = false;
            $message = 'Renseigner correctement le nom et le nombre d\'invités sur la table';
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
