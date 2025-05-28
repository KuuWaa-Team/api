<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Invitation;
use App\Entity\Table;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use App\Service\CreditService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class OpenBookController extends AbstractController
{
    private  $bookPrice = 6;

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly CreditService $creditService,
    ) {
    }

    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $user = $event->getUser();
        $message = "Livre d'or dévérouillé avec succeès";
        $success = true;

        if ($user === null || $user->getCredits() < $this->bookPrice) {
            $message = "Solde insifussant";
            $success = false;
        } else {
            $event->setHasBook(true);
            $this->eventRepository->save($event);
            $this->creditService->reduce($user, $this->bookPrice);
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'credits'   => $user->getCredits(),
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
