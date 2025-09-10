<?php

namespace App\Controller\Admin;

use App\Entity\NewsLetter;
use App\Repository\NewsLetterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class NewsLetterController extends AbstractController
{
    public function __construct(
        private readonly NewsLetterRepository $newsLetterRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/news/letter', name: 'app_admin_news_letter')]
    public function index(): Response
    {
        $newsletters = $this->newsLetterRepository->findAll();
        $normalizedData = $this->normalizer->normalize(
            $newsletters,
            null,
            ['groups' => ['newsletter:fRead']]
        );

        return $this->json([
            'success' => true,
            'data' => $normalizedData
        ]);
    }
}
