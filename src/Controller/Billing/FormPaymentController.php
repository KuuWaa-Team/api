<?php

namespace App\Controller\Billing;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormPaymentController extends AbstractController
{
    #[Route('/events/{id}/payment', name: 'app_event_payment')]
    public function index(): Response
    {
        return $this->render('billing/form_payment/index.html.twig', []);
    }
}
