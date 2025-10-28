<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LoginAdminController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly JWTTokenManagerInterface $jwtManager,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Données JSON invalides'
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = $content['email'] ?? null;
        $password = $content['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email et mot de passe requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            /**
             * @var User $user
             */
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $isValid = $user->isPasswordUpdated()
                ? $this->userPasswordHasher->isPasswordValid($user, $password)
                : ($password === 'Password');

            if (!$isValid) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Accès refusé : utilisateur non administrateur'
                ], Response::HTTP_FORBIDDEN);
            }

            $token = $this->jwtManager->create($user);
            $normalizedUser = $this->normalizer->normalize($user, null, ['groups' => 'user:pRead']);
            $normalizedUser = $this->normalizer->normalize($user, null, ['groups' => 'user:pRead']);

            return new JsonResponse([
                'success' => true,
                'message' => 'Connexion réussie',
                'token' => $token,
                'user' => $normalizedUser
            ], Response::HTTP_OK);

        } catch (Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer : ' . $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
