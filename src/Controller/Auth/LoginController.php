<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly JWTTokenManagerInterface $jWTTokenManager,
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $email       = $content['email'] ?? null;
        $uid         = $content['uid'] ?? null;
        $displayName = $content['displayName'] ?? 'Utilisateur';
        $photoUrl    = $content['photoUrl'] ?? null;
        $provider    = $content['provider'] ?? null;
        $phoneId     = $content['phoneId'] ?? Uuid::v4(); // fallback si tu veux en générer un

        if (!$email || !$uid) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email ou UID manquant',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        $new = false;

        if (!$user) {
            $user = (new User())
                ->setEmail($email);
            $new = true;
        }

        $user
            ->setDisplayName($displayName)
            ->setUid($uid)
            ->setPhotoUrl($photoUrl)
            ->setProvider($provider);

        $this->userRepository->save($user, true);

        $token = $this->jWTTokenManager->create($user);

        return new JsonResponse($this->normalizer->normalize([
            'success' => true,
            'message' => '',
            'new'     => $new,
            'user'    => $user,
            'token'   => $token,
        ], null, ['groups' => ['u.pRead']]), Response::HTTP_OK);
    }
}
