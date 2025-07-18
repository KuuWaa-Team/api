<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteAccountController extends AbstractController
{

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function __invoke(User $user): JsonResponse
    {
        $user->setDeletedAt(new \DateTimeImmutable());
        $this->userRepository->save($user);

        return new JsonResponse([
            'success' => true,
        ], Response::HTTP_OK);
    }
}