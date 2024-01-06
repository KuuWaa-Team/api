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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AddInvitationEventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly InvitationRepository $invitationRepository,
    ) {
    }

    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = 'Invitation ajoutée avec succès !';
        $success = true;
        $errorType = null;

        if (array_key_exists('civility', $content) && array_key_exists('name', $content) && $content['name'] !== NULL  && $content['civility'] !== NULL) {
            $name = $content['name'];
            $civility = $content['civility'];
            $small = strtolower($civility);
            $count = 1;
            switch ($small) {
                case 'couple':
                    $count = 2;
                    break;
                case 'groupe':
                    $count = $content['count'] ?? $count;
                    break;
            }
            $invitation = (new Invitation())
                ->setName($name)
                ->setCivility($civility)
                ->setCount($count);
            $event->addInvitation($invitation);
            $this->invitationRepository->save($invitation, false);
            $this->eventRepository->save($event, true);
        } else {
            $success = false;
            $message = 'Renseigner correctement les informations';
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
