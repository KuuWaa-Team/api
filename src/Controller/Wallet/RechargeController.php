<?php

namespace App\Controller\Wallet;

use App\Entity\User;
use App\Entity\Wallet\Operation;
use App\Repository\Wallet\OperationRepository;
use App\Service\Billing\PaymentService;
use App\Service\CreditService;
use App\Repository\UserRepository;
use App\Service\PhoneService;
use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RechargeController extends AbstractController
{
    public function __construct(
        private readonly PhoneService        $phoneService,
        private readonly CreditService       $creditService,
        private readonly OperationRepository $operationRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly PaymentService      $paymentService,
        private readonly UserRepository      $userRepository,
    ){}

    /**
     * @throws \JsonException
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $amount = $content['amount'];
        $paymentMethod = $content['payment_method'];
        $phoneNumber = $content['phone_number'];
        $userId = $content['user_id'];

        $user = $this->userRepository->find($userId);

        $success = true;
        $operation = null;

        if ($amount < WalletService::$MIN_RECHARGE) {
            $message = "Vous devez recharger au moins " . WalletService::$MIN_RECHARGE . " USD";
            $success = false;
        } if ($amount > WalletService::$MIN_RECHARGE * 100) {
            $message = "Vous devez recharger au plus " . (WalletService::$MIN_RECHARGE * 100) . " USD en une fois";
            $success = false;
        } else if (WalletService::$MOBILE_PAYMENT !== $paymentMethod && WalletService::$CARD_PAYMENT !== $paymentMethod) {
            $message = "Methode de paiement inconnue";
            $success = false;
        } else if (WalletService::$MOBILE_PAYMENT === $paymentMethod && !$this->phoneService->isValidPhoneNumber($phoneNumber)) {
            $message = "Numero de telephone invalide";
            $success = false;
        } else {
            $year = (new \DateTime())->format("y");
            $position = str_pad($this->operationRepository->countItems() + 1, 4, '0', STR_PAD_LEFT);

            $ref = "KW-R-$year-$position";
            $credits = floor($amount / $this->creditService->getCreditPrice());
            $operation = (new Operation())
                ->setUser($user)
                ->setCredits($credits)
                ->setType(WalletService::$OPERATION_RECHARGE)
                ->setDate(new \DateTimeImmutable())
                ->setPhoneNumber($phoneNumber)
                ->setTitle("Recharge des credits")
                ->setReference($ref)
                ->setStatus(WalletService::$STATUS_WAITING)
                ->setPaymentMethod($paymentMethod)
                ->setAmount($amount);
            $this->operationRepository->save($operation);

            $redirectUrl = ""; 
            if ($paymentMethod === WalletService::$MOBILE_PAYMENT) {
                $payment = $this->paymentService->mobilePayment($operation);
            } else if ($paymentMethod === WalletService::$CARD_PAYMENT) {
                $payment = $this->paymentService->cardPayment($operation);
                $redirectUrl = $payment['redirectUrl'];
            } else {
                $payment = [];
            }

            if ($payment['success']) {
                $orderNumber = $payment['orderNumber'];
                $message = $payment["message"];
                $operation->setOrderNumber($orderNumber);
                $operation->setRedirectUrl($redirectUrl);
                $this->operationRepository->save($operation);
            } else {
                $success = false;
                $message = "Une erreur s'est produite lors du paiement";
                $operation->setStatus(WalletService::$STATUS_FAILED);
                $this->operationRepository->save($operation);
            }
        }

        return new JsonResponse([
            'success'     => $success,
            'message'     => $message,
            'operation'   => $this->normalizer->normalize($operation, null, ['groups' => ['operation:pRead']]),
        ], Response::HTTP_CREATED);
    }
}