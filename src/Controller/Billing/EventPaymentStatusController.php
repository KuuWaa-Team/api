<?php

namespace App\Controller\Billing;

use App\Entity\Event;
use App\Entity\Payment\Payment;
use App\Repository\EventRepository;
use App\Repository\Payment\PaymentRepository;
use App\Service\Billing\MobilePaymentService;
use App\Service\PhoneService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints\IsFalse;

class EventPaymentStatusController extends AbstractController
{
    public function __construct(
        private readonly PhoneService $phoneService,
        private readonly MobilePaymentService $mobilePaymentService,
        private readonly EventRepository $eventRepository,
        private readonly PaymentRepository $paymentRepository,
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
//            try {
                $payment = $this->paymentRepository->findOneByOrderNumber($orderNumber);
                if (
                    $payment !== null
                ) {
                    do {
                        $response = $this->mobilePaymentService->status($payment->getOrderNumber());
                        $waiting = $response['waiting'];
                        if (!$waiting) {
                            if ($response['success']) {
                                $payment->setStatus('paid');
                                $event->setPaymentStatus('paid');
                                $this->eventRepository->save($event, false);
                                $this->paymentRepository->save($payment, true);
                                $success = true;
                                $message = $response['message'];
                            } else {
                                $success = false;
                                $message = $response['message'];
                            }
                        }
                    } while($waiting);
                }
//            } catch (\Throwable $e) {
//                $message = "Une erreur est survenue";
//            }
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
