<?php

namespace App\Controller\Billing;

use App\Entity\Event;
use App\Entity\Payment\Payment;
use App\Repository\EventRepository;
use App\Repository\Payment\PaymentRepository;
use App\Service\Billing\PaymentService;
use App\Service\PhoneService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormPaymentController extends AbstractController
{
    public function __construct(
        private readonly PhoneService      $phoneService,
        private readonly PaymentService    $mobilePaymentService,
        private readonly PaymentRepository $paymentRepository
    ){}

    #[Route('/events/{id}/payment', name: 'app_event_payment')]
    public function index(Event $event, Request $request): Response
    {
        $amount = 0;
        $amount += $event->getEventPack()->getPrice();

        foreach ($event->getFeatures() as $feature) {
            $amount += $feature->getPrice();
        }

        return $this->render('billing/form_payment/index.html.twig', ['event' => $event, 'amount' => $amount]);
    }
}
