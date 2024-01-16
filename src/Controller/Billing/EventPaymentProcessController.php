<?php

namespace App\Controller\Billing;

use App\Entity\Payment\Payment;
use App\Repository\EventRepository;
use App\Service\Billing\MobilePaymentService;
use App\Service\PhoneService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventPaymentProcessController extends AbstractController
{
    public function __construct(
        private readonly PhoneService $phoneService,
        private readonly MobilePaymentService $mobilePaymentService,
        private readonly EventRepository $eventRepository
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $phoneNumber = $content["phoneNumber"];
        $formattedPhoneNumber = $this->phoneService->format($phoneNumber);
        $eventId = $content["event"];
        $success = false;

        $amount = 0;

        $event = $this->eventRepository->find($eventId);
        $amount += $event->getEventPack()->getPrice();

        foreach ($event->getFeatures() as $feature) {
            $amount += $feature->getPrice();
        }

        try {
            $ref = "KW-" . uniqid('', true);
            $paymentSuccess = $this->mobilePaymentService->pay($formattedPhoneNumber, $ref, $amount);

            if ($paymentSuccess[0]) {
                $payment = (new Payment())
                    ->setEvent($event)
                    ->setPhoneNumber($formattedPhoneNumber)
                    ->setDate(new \DateTime())
                    ->setPaymentMethod('FlexPay')
                    ->setAmount($amount)
                    ->setCurrency("USD")
                    ->setRef($ref)
                    ->setOrderNumber($paymentSuccess[1])
                ;
                $success = true;
                $message = "Processus de paiement lancé";
            }
        } catch (\Throwable $e) {
            $message = "Paiement échoué";
        }

    }
}