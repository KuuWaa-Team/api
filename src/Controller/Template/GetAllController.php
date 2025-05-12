<?php

namespace App\Controller\Template;

use App\Repository\EventTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetAllController extends AbstractController
{
    public function __construct(
        private readonly EventTemplateRepository $templateRepository
    ) {}

    public function __invoke(): JsonResponse
    {
        $templates = $this->templateRepository->findAll();
        return new JsonResponse([
            'templates' => $templates
        ], Response::HTTP_OK);
    }
}