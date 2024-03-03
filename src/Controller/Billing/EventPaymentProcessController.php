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

class EventPaymentProcessController extends AbstractController
{
    public function __construct(
        private readonly PhoneService $phoneService,
        private readonly MobilePaymentService $mobilePaymentService,
        private readonly PaymentRepository $paymentRepository,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Event $event)
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $phoneNumber = $content["phoneNumber"];
        $formattedPhoneNumber = $this->phoneService->format($phoneNumber);
        $success = false;
        $message = "";

        $amount = 0;

        $amount += $event->getEventPack()->getPrice();

        foreach ($event->getFeatures() as $feature) {
            $amount += $feature->getPrice();
        }
        $orderNumber = '';
//        try {
            $ref = "KW-" . uniqid('', true);
            $paymentSuccess = $this->mobilePaymentService->pay($formattedPhoneNumber, $ref, $amount);
            if ($paymentSuccess[0]) {
                $orderNumber = $paymentSuccess[1];
                $payment = (new Payment())
                    ->setEvent($event)
                    ->setPhoneNumber($formattedPhoneNumber)
                    ->setDate(new \DateTime())
                    ->setPaymentMethod('FlexPay')
                    ->setAmount($amount)
                    ->setCurrency("USD")
                    ->setRef($ref)
                    ->setStatus('created')
                    ->setOrderNumber($paymentSuccess[1]);
                $this->paymentRepository->save($payment, true);
                $success = true;
                $message = "Processus de paiement en cours...";
            }
//        } catch (\Throwable $e) {
//            $message = "Paiement échoué $e";
//        }
        $response = [
            'success'   => $success,
            'message'   => $message,
            'orderNumber' => $orderNumber
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
