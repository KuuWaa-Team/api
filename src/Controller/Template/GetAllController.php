<?php

namespace App\Controller\Template;

use App\Repository\EventTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetAllController extends AbstractController
{
    public function __construct(
        private readonly EventTemplateRepository $templateRepository,
        private readonly NormalizerInterface $normalizer,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(): JsonResponse
    {
        $templates = $this->templateRepository->findAll();
        $response = $this->normalizer->normalize([
            'success' => true,
            'message' => '',
            'templates' => $templates
        ], null, ['groups' => ['templates:fRead']]);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}