<?php


namespace App\Controller\Auth;

use App\Entity\Otp;
use App\Entity\User;
use App\Repository\OtpRepository;
use App\Repository\UserRepository;
use App\Service\OTPService;
use App\Service\PhoneService;
use App\Service\SmsService;
use JsonException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LoginController extends AbstractController
{

    public function __construct(
        private readonly PhoneService                $phoneService,
        private readonly NormalizerInterface         $normalizer,
        private readonly JWTTokenManagerInterface    $jWTTokenManager,
        private readonly UserRepository              $userRepository,
        private readonly SmsService                  $SMSService,
        private readonly OTPService                  $OTPService,
        private readonly OtpRepository               $otpRepository,
    ) {
    }

    /**
     * @throws JsonException|ExceptionInterface
     * @throws \Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $success = false;
        $user = null;
        $errorType = null;
        $new = null;
        $token = null;
        $loginAt = null;
        $appSignature = '';

        if (array_key_exists('phoneNumber', $content) && $content['phoneNumber'] !== null) {
            $phoneId = $content['phoneId'] ?? null;
            if ($this->phoneService->isValidPhoneNumber($content['phoneNumber'])) {
                $phoneNumber = $this->phoneService->format($content['phoneNumber']);
                $user = $this->userRepository->findOneBy([
                    'phoneNumber' => $content['phoneNumber']
                ]);
                $success = true;
                if ($user === null) {
                    // new user
                    $user = (new User())
                        ->setPhoneNumber($content['phoneNumber'])
                        ->setPhoneId($phoneId);
                    $this->userRepository->save($user, true);
                    $new = true;
                } else {
                    $new = false;
                }
                $token = $this->jWTTokenManager->create($user);

                $code = $this->OTPService->generate();
                $otp = (new Otp())
                    ->setUser($user)
                    ->setOtp($code);
                $this->otpRepository->save($otp, true);
                $appSignature = $content['signature'] ?? '';

                if ($new) {
                    $sms = "Bienvenue chez KuuWaa!\n";
                } elseif ($user->getFirstname() !== null) {
                    $sms = "Salut {$user->getFirstname()}!\n";
                } else {
                    $sms = "Salut!\n";
                }
                $sms .= "Votre code de verification est : {$otp->getOtp()}\nCe code est valide pendant 5 minutes. Ne le partagez avec personne.";

                 $this->SMSService->send($phoneNumber, $sms);
                $message = "Un Code a été envoyé au numéro +$phoneNumber par SMS. Veuillez le saisir pour continuer";
                $message .= "\n\$appSignature";

                $loginAt = new \DateTime();
            } else {
                $message = "Le numéro entré est invalide.";
                $errorType = "invalid-phone-number";
            }
        } else {
            $message = "Vous devez entrer le numéro de téléphone";
            $errorType = "missing-phone-number";
        }

        $return = [
            'success'   => $success,
            'message'   => $message,
            'errorType' => $errorType,
            'new'       => $new,
            'user'      => $user,
            'login_at'  => $loginAt,
            'token'     => $token,
            'signture'  => $appSignature
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'user:pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
