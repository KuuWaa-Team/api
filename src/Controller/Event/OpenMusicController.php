<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\CreditService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OpenMusicController extends AbstractController
{
    private  $musicPrice = 1;

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly CreditService $creditService,
    ) {
    }

    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $user = $event->getUser();
        $message = "Music dévérouillée avec succeès";
        $success = true;

        if ($user === null || $user->getCredits() < $this->musicPrice) {
            $message = "Solde insifussant";
            $success = false;
        } else {
            $event->setHasMusic(true);
            $this->eventRepository->save($event);
            $this->creditService->reduce($user, $this->musicPrice);
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'credits'   => $user->getCredits(),
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
