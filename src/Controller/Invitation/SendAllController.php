<?php

namespace App\Controller\Invitation;

use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Service\PhoneService;
use App\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SendAllController extends AbstractController
{
    public function __construct(
        private readonly PhoneService  $phoneService,
        private readonly SmsService $smsService,
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly EventRepository $eventRepository,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     * @throws \JsonException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $method = $content["method"] ?? null;
        $eventId = $content["eventId"] ?? null;

        $event = $this->eventRepository->find($eventId);

        $invitations = $event->getInvitations();

        foreach ($invitations as $invitation) {
            $link = "http://localhost:8000/invitations/" . $invitation->getId();

            $message = "Bonjour " . $invitation->getCivility() . " " . $invitation->getName();
            $message .= "\n\nVous êtes invité pour un évenement pour au " . $invitation->getEvent()->getDate()->format('d/m/Y') . " (" . $invitation->getEvent()->getName() . ")";
            $message .= "\n\nVeuillez cliquer sur le lien ci-dessous pour voir votre invitation";
            $message .= "\n\n" . $link;

            $phoneNumber = $this->phoneService->format($invitation->getPhoneNumber());
            if ($method === "sms") {
                $this->smsService->send($phoneNumber, $message);
            }

            $invitation->setStatus("sent");
            $this->invitationRepository->save($invitation, true);
        }

        $invitations = $this->invitationRepository->findByEvent($event);

        $response = [
            'success'   => true,
            'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
            'message'   => "Invitations envoyées avec succès",
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

}