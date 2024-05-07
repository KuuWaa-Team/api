<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebSideController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('public/web_side/index.html.twig', [
            'controller_name' => 'WebSideController',
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
