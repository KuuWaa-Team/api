<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\OtpRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OtpVerificationController extends AbstractController
{
    public function __construct(
        private readonly OtpRepository $otpRepository,
    )
    {
    }

    /**
     * @throws \JsonException
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = null;
        $success = false;
        $errorType = null;

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user === null) {
            $message = "Informations invalid";
            $errorType = "auth-error";
        } else if (array_key_exists('otp', $content) && $content['otp'] !== null) {
            $otp = $content['otp'];
            if ($this->otpRepository->validOtp($otp, $user)) {
                $success = true;
            } else {
                $message = "Code invalide";
                $errorType = "invalid-otp";
            }
        } else {
            $message = "Vous devez entrer l'otp";
            $errorType = "missing-otp";
        }

        $response = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
            'new' => $user->getFirstname() === null || $user->getLastname() === null || $user->getAddress() === null
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}