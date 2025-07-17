<?php

namespace App\Controller\Invitation;

use App\Entity\Invitation;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Service\ApiService;
use App\Service\CreditService;
use App\Service\DateService;
use App\Service\PhoneService;
use App\Service\SmsService;
use App\Service\TelegramService;
use App\Service\WhatsappService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class SendInvitationController extends AbstractController
{
    public function __construct(
        private readonly InvitationRepository $invitationRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly CreditService $creditService,
        private readonly ApiService $apiService,
        private readonly UserRepository $userRepository,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     * @throws \JsonException
     * @throws TransportExceptionInterface
     */
    public function __invoke(Request $request, Invitation $invitation): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $userId = $content['userId'];
        $user = $this->userRepository->find($userId);

        if ($user === null || $user->getCredits() < $invitation->getEvent()->getTemplate()->getPrice()) {
            $message = "Solde insifussant";
            $success = false;
        } else {
            $message = '';
            $success = true;

            $this->creditService->reduce($user, $invitation->getEvent()->getTemplate()->getPrice());
            $invitation->setStatus("sent");
            $this->invitationRepository->save($invitation);
        }

        $response = [
            'success'   => $success,
            'credits'   => $user->getCredits(),
            'message'   => $message
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}