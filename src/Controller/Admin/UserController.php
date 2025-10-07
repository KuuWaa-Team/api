<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException("Veuillez vous connecter", 1);
        }

        $user->setCountNewNewsletters(0);
        $this->userRepository->save($user);
        
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

    #[Route('/admin/user/my-notifications/{id}', name: 'app_admin_get_notifications')]
    public function myNotifications(User $user): Response
    {   
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
