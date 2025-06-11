<?php

namespace App\Controller\Public;

use App\Repository\EventPackRepository;
use App\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebSideController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EventPackRepository $packRepository, Request $request, SmsService $smsService): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $phone = $request->request->get('phone');
            $subject = $request->request->get('subject');
            $message = $request->request->get('message');

            if (empty($phone) || empty($subject) || empty($message)) {
                $this->addFlash('error', 'Vous devez entrer tous les champs');
            } else {
                $smsService->send('243814063056', "Formulaire de contact\n-----------------------\n\nDestinateur : $phone\nSujet : $subject\nMessage : $message\n
                ");
                $this->addFlash('success', 'Message envoyé avec succès');
            }
        }
        return $this->render('public/web_side/index2.html.twig', [
            'packs' => $packRepository->findAllOrdered(),
        ]);
    }

    #[Route('/service', name: 'app_service')]
    public function service(): Response
    {
        return $this->render('public/web_side/service.html.twig', [
            'controller_name' => 'WebSideController',
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('public/web_side/about.html.twig', [
            'controller_name' => 'WebSideController',
        ]);
    }

    #[Route('/faq', name: 'app_faq')]
    public function faq(): Response
    {
        return $this->render('public/web_side/faq.html.twig', [
            'controller_name' => 'WebSideController',
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('public/web_side/contact.html.twig', [
            'controller_name' => 'WebSideController',
        ]);
    }

    #[Route('/privacy', name: 'app_contact')]
    public function privacy(): Response
    {
        return $this->render('public/web_side/privacy.html.twig');
    }
}
