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
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use JsonException;

class LoginController extends AbstractController
{

    public function __construct(
        private readonly NormalizerInterface         $normalizer,
        private readonly JWTTokenManagerInterface    $jWTTokenManager,
        private readonly UserRepository              $userRepository,
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws JsonException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode(
            $request->getContent(),
            true,
            512, JSON_THROW_ON_ERROR
        );

        $success        = false;
        /**
         * @var null|User $user
         */
        $user           = null;
        $token          = null;
        $new            = false;
        $message        = "";

//        try {
            $email       = $content['email'];
            $uuid        = $content['uid'];
            $displayName = $content['displayName'];
            $photoUrl    = $content['photoURL'];
            $provider    = $content['provider'];
            $phoneId     = $content['phoneId'];

            $user = $this->userRepository->findOneBy(['email' => $email]);
            $success = true;

            if ($user === null) {
                $user = (new User())
                    ->setSlug()
                    ->setCreatedAt()
                    ->setUpdatedAt()
                    ->setEmail($email);
                $new = true;
            }

            $user
                ->setDisplayName($displayName)
                ->setUid($uuid)
                ->setPhotoUrl($photoUrl)
                ->setProvider($provider);

            $this->userRepository->save($user, true);

//            $token = $this->jWTTokenManager->create($user);

            if ($new) {
                $welcomeMessage = "Bienvenue chez KuuWaa!\nLa meuilleur application de gestion d'evenement";
                // ... send mail
            }
//        } catch (Exception $exception) {
//            $message = "Une erreur est survenue. Veuillez ressayer";
//        }
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
