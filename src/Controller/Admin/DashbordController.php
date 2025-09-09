<?php

namespace App\Controller\Admin;

use App\Repository\EventRepository;
use App\Repository\Payment\PaymentRepository;
use App\Repository\UserRepository;
use App\Repository\Wallet\OperationRepository;
use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class DashbordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventRepository $eventRepository,
        private readonly OperationRepository $operationRepository,
    ){}

    #[Route('/admin/dashbord', name: 'app_admin_dashbord', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $actifUsers = $this->userRepository->count([]);
        $events = $this->eventRepository->count([]);

        $qb = $this->operationRepository->createQueryBuilder('o')
            ->select('SUM(o.amount)')
            ->where('p.status = :status')
            ->setParameter('status', WalletService::$STATUS_PAID)
            ->getQuery();

        $money = (float) $qb->getSingleScalarResult();

        return $this->json([
            'success' => true,
            'data' => [
                'actifUsers' => $actifUsers,
                'events' => $events,
                'paymentsAmount' => $money,
            ]
        ]);
    }
}
