<?php

namespace App\Controller\Billing;

use App\Entity\Event;
use App\Entity\Payment\Payment;
use App\Repository\EventRepository;
use App\Repository\Payment\PaymentRepository;
use App\Service\Billing\PaymentService;
use App\Service\PhoneService;
use App\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints\IsFalse;

class EventPaymentStatusController extends AbstractController
{
    public function __construct(
        private readonly PhoneService      $phoneService,
        private readonly PaymentService    $mobilePaymentService,
        private readonly EventRepository   $eventRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly SmsService        $smsService,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Event $event)
    {
        $waiting = true;
        $success = false;
        $message = '';
        if ($request->get('on') !== null) {
            $orderNumber = $request->get('on');
            try {
                $payment = $this->paymentRepository->findOneByOrderNumber($orderNumber);
                if (
                    $payment !== null
                ) {
                    do {
                        $response = $this->mobilePaymentService->status($payment->getOrderNumber(), $payment->getPhoneNumber());
                        $waiting = $response['waiting'];
                        if (!$waiting) {
                            if ($response['success']) {
                                $payment->setStatus('paid');
                                $event->setPaymentStatus('paid');
                                $this->eventRepository->save($event, false);
                                $this->paymentRepository->save($payment, true);

                                $this->smsService->send($payment->getPhoneNumber(), "Bonjour " . $event->getUser()->getFirstname() . ", \n"
                                  . "Votre événement '" . $event->getCategory()->getName() . " - " . $event->getName() . "' vient d'être activé. Vous pouvez dès à présent partager vos invitations. \n\nMerci de faire confiance à KuuWaa."
                                );

                                $success = true;
                            } else {
                                $success = false;
                            }
                            $message = $response['message'];
                        }
                    } while($waiting);
                }
            } catch (\Throwable $e) {
                $message = "Une erreur est survenue";
            }
        } else {
            $message = "Parametre orderNumber (on) manquant";
        }

        $response = [
            'success' => $success,
            'waiting' => $waiting,
            'message' => $message,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
