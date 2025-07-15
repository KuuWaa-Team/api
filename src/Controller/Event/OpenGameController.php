<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\CreditService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OpenGameController extends AbstractController
{
    private  $gamePrice = 1;

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly CreditService $creditService,
    ) {
    }

    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $user = $event->getUser();
        $message = "Jeux dévérouillés avec succeès";
        $success = true;

        if ($user === null || $user->getCredits() < $this->gamePrice) {
            $message = "Solde insifussant";
            $success = false;
        } else {
            $event->setHasGame(true);
            $this->eventRepository->save($event);
            $this->creditService->reduce($user, $this->gamePrice);
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'credits'   => $user->getCredits(),
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
