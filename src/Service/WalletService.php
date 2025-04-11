<?php

namespace App\Service;

use App\Entity\Wallet\Operation;
use App\Repository\Wallet\OperationRepository;
use App\Service\Billing\PaymentService;

class WalletService
{
    public static string $MOBILE_PAYMENT = "MOBILE_PAYMENT";
    public static string $CARD_PAYMENT = "CARD_PAYMENT";

    public static string $OPERATION_RECHARGE = "OPERATION_RECHARGE";
    public static string $OPERATION_USE = "OPERATION_USE";

    public static string $STATUS_WAITING = "STATUS_WAITING";
    public static string $STATUS_PAID = "STATUS_PAID";
    public static string $STATUS_FAILED = "STATUS_FAILED";

    public static int $MIN_RECHARGE = 5;

    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly CreditService $creditService,
        private readonly NotificationService $notificationService,
        private readonly OperationRepository $operationRepository,
    ){}

    public function checkPaymentStatus (Operation $operation): void {
        try {
            $response = $this->paymentService->checkPaymentStatus($operation);
            $waiting = $response['waiting'];
            if (!$waiting) {
                if ($response['success']) {
                    $this->creditService->rechargeUser($operation->getUser(), $operation->getCredits());
                    $operation->setStatus(self::$STATUS_PAID);

                    $this->notificationService->sendToOne(
                        deviceToken: $operation->getUser()?->getPhoneId() ?? '',
                        title: "Paiement validé",
                        body: "Votre recharge de " . $operation->getCredits() . " crédits a ete validé avec succès.",
                        data: ["credits" => $operation->getUser()?->getCredits() ?? 0]
                    );
                } else {
                    $operation->setStatus(self::$STATUS_FAILED);
                }
                $this->operationRepository->save($operation);
            }
        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }

}