<?php

namespace App\Controller\Wallet;

use App\Entity\User;
use App\Entity\Wallet\Operation;
use App\Repository\Wallet\OperationRepository;
use App\Service\Billing\PaymentService;
use App\Service\CreditService;
use App\Service\WalletService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CheckPaymentStatusController extends AbstractController
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    public function __invoke(Operation $operation, Request $request): JsonResponse
    {
        /**
         * @var User $user
         */
        $user    = $this->getUser();
        $waiting = true;
        $success = false;
        $message = "";


        if ($operation->isPaid()) {
            return new JsonResponse(['message' => 'Invalid payment process'], Response::HTTP_BAD_REQUEST);
        }

        $this->walletService->checkPaymentStatus($operation);

        $response = [
            'success' => $success,
            'waiting' => $waiting,
            'message' => $message,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}