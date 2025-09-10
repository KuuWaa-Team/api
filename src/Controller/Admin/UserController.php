<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $normalizer
    ){}

    #[Route('/admin/user', name: 'app_admin_user')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();
        $normalizedData = $this->normalizer->normalize(
            $users,
            null,
            ['groups' => ['user:pRead']]
        );

        return $this->json([
            'success' => true,
            'data' => $normalizedData
        ]);
    }
    }
}
