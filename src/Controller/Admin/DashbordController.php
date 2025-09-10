<?php

namespace App\Controller\Admin;

use App\Repository\EventRepository;
use App\Repository\Payment\PaymentRepository;
use App\Repository\UserRepository;
use App\Repository\Wallet\OperationRepository;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class DashbordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventRepository $eventRepository,
        private readonly OperationRepository $paymentRepository,
        private readonly EntityManagerInterface $em,
    ){}

    #[Route('/admin/dashbord', name: 'app_admin_dashbord', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $actifUsers = $this->userRepository->count([]);
        $eventsTotal = $this->eventRepository->count([]);

        $qb = $this->paymentRepository->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->andWhere("p.phoneNumber not like '%999999999'")
            ->setParameter('status', WalletService::$STATUS_PAID);
        $paymentsAmount = (float) $qb->getQuery()->getSingleScalarResult();

        $startOfMonth = new \DateTime('first day of this month 00:00:00');
        $endOfMonth   = new \DateTime('last day of this month 23:59:59');

        // Utilisateurs ce mois
        $usersThisMonth = $this->userRepository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt BETWEEN :start AND :end')
            ->setParameters(['start' => $startOfMonth, 'end' => $endOfMonth])
            ->getQuery()
            ->getSingleScalarResult();

        // Événements créés ce mois
        $eventsThisMonth = $this->eventRepository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.createdAt BETWEEN :start AND :end')
            ->setParameters(['start' => $startOfMonth, 'end' => $endOfMonth])
            ->getQuery()
            ->getSingleScalarResult();

        // Paiements réussis ce mois
        $paymentsThisMonth = $this->paymentRepository->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->andWhere("p.phoneNumber not like '%999999999'")
            ->andWhere('p.date BETWEEN :start AND :end')
            ->setParameters([
                'status' => WalletService::$STATUS_PAID,
                'start' => $startOfMonth,
                'end' => $endOfMonth
            ])
            ->getQuery()
            ->getSingleScalarResult();

        // 📌 Données des 10 derniers mois (recharges = paiements réussis)
        $statsLast10Months = [];
        $now = new \DateTime();

        for ($i = 9; $i >= 0; $i--) {
            $monthStart = (clone $now)->modify("-$i months")->modify('first day of this month 00:00:00');
            $monthEnd   = (clone $now)->modify("-$i months")->modify('last day of this month 23:59:59');
            $label = $monthStart->format('Y-m');

            // événements
            $eventsCount = $this->eventRepository->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.createdAt BETWEEN :start AND :end')
                ->setParameters(['start' => $monthStart, 'end' => $monthEnd])
                ->getQuery()
                ->getSingleScalarResult();

            // recharges (paiements réussis)
            $recharges = $this->paymentRepository->createQueryBuilder('p')
                ->select('SUM(p.amount)')
                ->where('p.status = :status')
                ->andWhere("p.phoneNumber not like '%999999999'")
                ->andWhere('p.date BETWEEN :start AND :end')
                ->setParameters([
                    'status' => WalletService::$STATUS_PAID,
                    'start' => $monthStart,
                    'end' => $monthEnd
                ])
                ->getQuery()
                ->getSingleScalarResult() ?? 0;

            $usersCount = $this->userRepository->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.createdAt BETWEEN :start AND :end')
                ->setParameters(['start' => $monthStart, 'end' => $monthEnd])
                ->getQuery()
                ->getSingleScalarResult();

            $statsLast10Months[] = [
                'month' => $label,
                'events' => (int) $eventsCount,
                'users' => (int) $usersCount,
                'recharges' => (float) $recharges,
            ];
        }

        return $this->json([
            'success' => true,
            'data' => [
                'global' => [
                    'actifUsers' => $actifUsers,
                    'events' => $eventsTotal,
                    'failedPaymentsAmount' => $paymentsAmount,
                ],
                'thisMonth' => [
                    'newUsers' => (int) $usersThisMonth,
                    'events' => (int) $eventsThisMonth,
                    'recharges' => (float) $paymentsThisMonth,
                ],
                'last10Months' => $statsLast10Months,
            ]
        ]);
    }
}
