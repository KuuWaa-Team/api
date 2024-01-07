<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\OtpRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CompleteProfileController extends AbstractController
{
    public function __construct(
        private readonly UserRepository      $userRepository,
        private readonly NormalizerInterface $normalizer,
    )
    {
    }

    /**
     * @throws \JsonException
     * @throws NonUniqueResultException
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request, User $user): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = null;
        $errorType = null;

        $lastname = $content['lastname'];
        $firstname = $content['firstname'];
        $address = $content['address'];

        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setAddress($address)
        ;

        $this->userRepository->save($user, true);

        $return = [
            'success'   => true,
            'message'   => $message,
            'errorType' => $errorType,
            'user'      => $user
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'user:pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}