<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
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
        private readonly NormalizerInterface         $normalizer,
        private readonly UserRepository              $userRepository,
    ) {
    }

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

        try {
            $email       = $content['email'];
            $uuid        = $content['uid'];
            $displayName = $content['displayName'];
            $photoUrl    = $content['photoUrl'] ?? '';
            $provider    = $content['provider'];
            $phoneId     = $content['phoneId'];
            $token       = $content['firebase_token'];

            $user = $this->userRepository->findOneBy(['email' => $email]);
            $new = false;
            $success = true;

            if ($user === null) {
                $user = (new User())
                    ->setSlug()
                    ->setCreatedAt()
                    ->setUpdatedAt()
                    ->setCredits(0)
                    ->setDisplayName($displayName)
                    ->setUid($uuid)
                    ->setPhotoUrl($photoUrl)
                    ->setProvider($provider)
                    ->setPhotoUrl($photoUrl)
                    ->setEmail($email);
                $new = true;
            }

            $user->setPhoneId($phoneId);
            $this->userRepository->save($user, true);

            if ($new) {
                $welcomeMessage = "Bienvenue chez KuuWaa!\nLa meuilleur application de gestion d'evenement";
                // ... send mail
            }
        } catch (Exception $exception) {
            $success = false;
            $message = "Une erreur est survenue. Veuillez ressayer";
        }
        $return = [
            'success'   => $success,
            'message'   => $message,
            'new'       => $new,
            'user'      => $user,
            'token'     => $token,
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'user:pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
