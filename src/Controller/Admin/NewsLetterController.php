<?php

namespace App\Controller\Admin;

use App\Entity\NewsLetter;
use App\Entity\User;
use App\Repository\NewsLetterRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class NewsLetterController extends AbstractController
{
    public function __construct(
        private readonly NewsLetterRepository $newsLetterRepository,
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/news/letter', name: 'app_admin_news_letter')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException("Veuillez vous connecter", 1);
        }

        $user->setCountNewNewsletters(0);
        $this->userRepository->save($user);
        
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
