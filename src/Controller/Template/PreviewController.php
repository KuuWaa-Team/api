<?php

namespace App\Controller\Template;

use App\Entity\Event\EventTemplate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/templates/preview', name: 'templates.')]
final class PreviewController extends AbstractController
{
    #[Route('/{id}', name: 'preview')]
    public function index(EventTemplate $template): Response
    {
        return $this->render("template/preview/{$template->getCode()}.html.twig");
    }
}
