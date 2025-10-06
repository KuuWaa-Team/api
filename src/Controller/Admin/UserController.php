<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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

    #[Route('/admin/user/my-notifications', name: 'app_admin_get_notifications')]
    #[IsGranted(['ROLE_USER'])]
    public function myNotifications(): Response
    {   
        /**
         * @var User? $user
         */
        $user = $this->getUser();
        return $this->json([
            'countNewEvents' => $user->getCountNewEvents(),
            'countNewUsers' => $user->getCountNewUsers(),
            'countNewDownloadRequests' => $user->getCountNewDownloadRequests(),
            'countNewNewsletters' => $user->getCountNewNewsletters(),
            'countNewRecharges' => $user->getCountNewRecharges(),
            'countNewContacts' => $user->getCountNewContacts(),
        ]);
    }
}
