<?php

namespace App\Controller\Invitation;

use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Service\DateService;
use App\Service\PhoneService;
use App\Service\SmsService;
use App\Service\WhatsappService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class SendAllController extends AbstractController
{
    public function __construct(
        private readonly PhoneService  $phoneService,
        private readonly SmsService $smsService,
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly WhatsappService $whatsappService,
        private readonly EventRepository $eventRepository,
        private readonly DateService $dateService,
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

        if ($event->getPaymentStatus() === 'paid') {
            $invitations = $event->getInvitations();

            foreach ($invitations as $invitation) {
                if ($invitation->getStatus() == 'created') {
                    $phoneNumber = $this->phoneService->format($invitation->getPhoneNumber());
                    if ($method === "sms") {
                        $message = "Bonjour " . $invitation->getCivility() . " " . $invitation->getName();
                        $message .= ",\nVous etes invite a l'evenement \"{$invitation->getEvent()->getName()}\" le " . $this->dateService->getFullDate($invitation->getEvent()->getDate(), true, true) . " a {$invitation->getEvent()->getDate()->format('H:i')}.";
                        $message .= "\n\nPour acceder e votre invitation, veuillez ouvrir le lien suivant.";
                        $message .= "\nhttp://kuuwaa.com/invitations/" . $invitation->getCode();
                        $message .= "\n\nSi vous rencontrez des difficultes pour ouvrir le lien ou pour acceder a l'application KuuWaa, veuillez retenir le code d'invitation a 6 chiffres ({$invitation->getCode()}) et présentez-le lors de la vérification a l'entree de l'evenement.";
        
                        $body = "$message\n\nCeci est un message automatique, veuillez ne pas y repondre.";
                        $this->smsService->send($phoneNumber, $body);
                    } else if ($method === "wpp") {
                        $to = "whatsapp:+$phoneNumber";
                        $message = "Bonjour " . $invitation->getCivility() . " " . $invitation->getName();
                        $message .= ",\nVous êtes invité à l'évenement \"{$invitation->getEvent()->getCategory()->getName()} - {$invitation->getEvent()->getName()}\" le " . $this->dateService->getFullDate($invitation->getEvent()->getDate(), true, true)  . " à {$invitation->getEvent()->getDate()->format('H:i')}.";
                        $message .= "\n\nPour accéder à votre invitation, veuillez ouvrir le lien suivant.";
                        $message .= "\nhttp://kuuwaa.com/invitations/" . $invitation->getCode();
                        $message .= "\n\nSi vous rencontrez des difficultés pour ouvrir le lien ou pour accéder à l'application KuuWaa, veuillez retenir le code d'invitation à 6 chiffres ({$invitation->getCode()}) et présentez-le lors de la vérification à l'entrée de l'évenement.";
        
                        $body = "$message\n\nCeci est un message automatique, veuillez ne pas y repondre.";
                        try {
                            $messageSid = $this->whatsappService->sendWhatsAppMessage($to, $body);
                        } catch (ConfigurationException|TwilioException $e) {
                            dd($e);
                        }
                    } else {
                        // error
                    }
        
                    $invitation->setStatus("sent");
                    $this->invitationRepository->save($invitation, true);
                }
            }
            $success = true;
            $notpaid = false;
            $message = "Invitations envoyées avec succès";
        } else {
            $success = false;
            $notpaid = true;
            $message = "Une erreur est survenue";
        }

        $invitations = $this->invitationRepository->findByEvent($event);

        $response = [
            'success'   => $success,
            'notpaid' => $notpaid,
            'invitations' => $this->normalizer->normalize($invitations, null, ['groups' => ['invitation:pRead']]),
            'message'   => $message,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

}