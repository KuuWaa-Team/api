<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TableRepository $tableRepository,
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request)
    {
        $success = true;
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $civility = $content["civility"] ?? null;
        $name = $content["name"] ?? null;
        $phoneNumber = $content["phoneNumber"] ?? null;
        $count = $content["count"] ?? null;
        $eventId = $content["eventId"] ?? null;
        $tableId = $content["tableId"] ?? null;

        $event = $this->eventRepository->find($eventId);

        $invitation = (new Invitation())
            ->setName($name)
            ->setPhoneNumber($phoneNumber)
            ->setEvent($event)
            ->setCivility($civility)
            ->setStatus("created")
            ->setCount($count)
        ;

        if ($tableId !== null) {
            $table = $this->tableRepository->find($tableId);
            $countTable = $table->getCount() ?? 0;
            $countTableTaken = $table->getCountTaken() ?? 0;

            if ($countTable < $countTableTaken + $count) {
                $response = [
                    'success'   => false,
                    'message'   => "Pas assez de place à la table {$table->getName()} table pour cette invitation",
                ];

                return new JsonResponse($response, Response::HTTP_OK);
            }

            $invitation->setInvitationTable($table);
            $table->setCountTaken($countTableTaken + $count);
            $this->tableRepository->save($table, true);
        }

        $this->invitationRepository->save($invitation, true);

        $eventInvitationsCount = $event->getInvitationsCount() ?? 0;

        $event->setInvitationsCount($eventInvitationsCount + 1);
        $this->eventRepository->save($event, true);

        $invitations = $this->invitationRepository->findByEvent($invitation->getEvent());
        $response = [
            'success'   => true,
            'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
            'message'   => "Invitation enrégistrée avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}