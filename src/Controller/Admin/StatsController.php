<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\Payment\PaymentRepository;
use App\Repository\Wallet\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class StatsController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventRepository $eventRepository,
        private readonly OperationRepository $paymentRepository,
        private readonly OperationRepository $operationRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/admin/stats', name: 'app_admin_stats', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $now = new \DateTimeImmutable();
        $firstDayOfMonth = $now->modify('first day of this month')->setTime(0, 0, 0);
        $lastDayOfMonth = $now->modify('last day of this month')->setTime(23, 59, 59);
        $start12Months = $now->modify('-11 months')->modify('first day of this month')->setTime(0, 0, 0);

        // === UTILISATEURS ===
        $totalUsers = $this->userRepository->count([]);

        $newUsersThisMonth = $this->userRepository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $firstDayOfMonth)
            ->setParameter('end', $lastDayOfMonth)
            ->getQuery()
            ->getSingleScalarResult();

        $users = $this->userRepository->createQueryBuilder('u')
            ->where('u.createdAt >= :start')
            ->setParameter('start', $start12Months)
            ->getQuery()
            ->getResult();

        $usersEvolution = array_fill_keys(
            array_map(fn($i) => $now->modify("-$i months")->format('Y-m'), range(11, 0)),
            0
        );
        foreach ($users as $user) {
            $month = $user->getCreatedAt()->format('Y-m');
            if (isset($usersEvolution[$month])) $usersEvolution[$month]++;
        }

        // === EVENTS ===
        $totalEvents = $this->eventRepository->count([]);

        $eventsThisMonth = $this->eventRepository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $firstDayOfMonth)
            ->setParameter('end', $lastDayOfMonth)
            ->getQuery()
            ->getSingleScalarResult();

        $events = $this->eventRepository->createQueryBuilder('e')
            ->where('e.createdAt >= :start')
            ->setParameter('start', $start12Months)
            ->getQuery()
            ->getResult();

        $eventsEvolution = array_fill_keys(
            array_map(fn($i) => $now->modify("-$i months")->format('Y-m'), range(11, 0)),
            0
        );
        foreach ($events as $event) {
            $month = $event->getCreatedAt()->format('Y-m');
            if (isset($eventsEvolution[$month])) $eventsEvolution[$month]++;
        }

        // Top 5 événements avec le plus d'invitations
        $topEvents = $this->em->createQuery("
            SELECT e.title, COUNT(i.id) as invitations
            FROM App\Entity\Event e
            JOIN e.invitations i
            GROUP BY e.id
            ORDER BY invitations DESC
        ")->setMaxResults(5)->getResult();

        // === PAYMENTS ===
        $totalAmount = $this->paymentRepository->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->setParameter('status', 'SUCCESS')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $failedAmount = $this->paymentRepository->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->setParameter('status', 'FAILED')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $amountThisMonth = $this->paymentRepository->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->andWhere('p.date BETWEEN :start AND :end')
            ->setParameter('status', 'SUCCESS')
            ->setParameter('start', $firstDayOfMonth)
            ->setParameter('end', $lastDayOfMonth)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $transactions = $this->em->createQuery("
            SELECT 
                SUM(CASE WHEN p.status = 'SUCCESS' THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN p.status = 'FAILED' THEN 1 ELSE 0 END) as failed
            FROM App\Entity\Payment\Payment p
        ")->getSingleResult();

        $payments = $this->paymentRepository->createQueryBuilder('p')
            ->where('p.status = :status')
            ->andWhere('p.date >= :start')
            ->setParameter('status', 'SUCCESS')
            ->setParameter('start', $start12Months)
            ->getQuery()
            ->getResult();

        $paymentsEvolution = array_fill_keys(
            array_map(fn($i) => $now->modify("-$i months")->format('Y-m'), range(11, 0)),
            0
        );
        foreach ($payments as $p) {
            $month = $p->getCreatedAt()->format('Y-m');
            if (isset($paymentsEvolution[$month])) $paymentsEvolution[$month] += $p->getAmount();
        }

        $topUser = $this->em->createQuery("
            SELECT u.id, u.fullname, SUM(o.amount) as credits
            FROM App\Entity\User u
            JOIN u.operations o
            GROUP BY u.id
            ORDER BY credits DESC
        ")->setMaxResults(1)->getOneOrNullResult();

        $totalCreditsBalance = $this->em->createQuery("
            SELECT SUM(o.amount) FROM App\Entity\Wallet\Operation o
        ")->getSingleScalarResult() ?? 0;

        // === ENGAGEMENT ===
        $avgEventsPerUser = $totalUsers > 0 ? $totalEvents / $totalUsers : 0;

        $activeUsersThisMonth = $this->operationRepository->createQueryBuilder('o')
            ->select('COUNT(DISTINCT o.user)')
            ->where('o.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $firstDayOfMonth)
            ->setParameter('end', $lastDayOfMonth)
            ->getQuery()
            ->getSingleScalarResult();

        $activeRate = $totalUsers > 0 ? round(($activeUsersThisMonth / $totalUsers) * 100, 2) . '%' : '0%';

        $operations = $this->operationRepository->createQueryBuilder('o')
            ->where('o.createdAt >= :start')
            ->setParameter('start', $start12Months)
            ->getQuery()
            ->getResult();

        $engagementEvolution = array_fill_keys(
            array_map(fn($i) => $now->modify("-$i months")->format('Y-m'), range(11, 0)),
            0
        );
        foreach ($operations as $op) {
            $month = $op->getCreatedAt()->format('Y-m');
            if (isset($engagementEvolution[$month])) $engagementEvolution[$month]++;
        }

        // === PERFORMANCE ===
        $lastMonthStart = $now->modify('first day of last month')->setTime(0,0,0);
        $lastMonthEnd = $now->modify('last day of last month')->setTime(23,59,59);

        $lastMonthPayments = $this->paymentRepository->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->andWhere('p.date BETWEEN :start AND :end')
            ->setParameter('status', 'SUCCESS')
            ->setParameter('start', $lastMonthStart)
            ->setParameter('end', $lastMonthEnd)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $progressionPayments = $lastMonthPayments > 0 
            ? round((($amountThisMonth - $lastMonthPayments) / $lastMonthPayments) * 100, 2) . '%'
            : 'N/A';

        $failureRate = $transactions['failed'] + $transactions['success'] > 0
            ? round(($transactions['failed'] / ($transactions['failed'] + $transactions['success'])) * 100, 2) . '%'
            : '0%';

        return $this->json([
            'users' => [
                'total' => (int) $totalUsers,
                'new_this_month' => (int) $newUsersThisMonth,
                'evolution_last_12_months' => $usersEvolution,
            ],
            'events' => [
                'total' => (int) $totalEvents,
                'created_this_month' => (int) $eventsThisMonth,
                'top_5_by_invitations' => $topEvents,
                'evolution_last_12_months' => $eventsEvolution,
            ],
            'payments' => [
                'total_amount' => (float) $totalAmount,
                'failed_amount' => (float) $failedAmount,
                'amount_this_month' => (float) $amountThisMonth,
                'transactions' => $transactions,
                'evolution_last_12_months' => $paymentsEvolution,
                'top_user' => $topUser,
                'total_credits_balance' => (float) $totalCreditsBalance,
            ],
            'engagement' => [
                'avg_events_per_user' => round($avgEventsPerUser, 2),
                'active_rate_this_month' => $activeRate,
                'evolution_last_12_months' => $engagementEvolution,
            ],
            'performance' => [
                'month_comparison' => [
                    'payments' => $progressionPayments,
                ],
                'payment_failure_rate' => $failureRate,
            ],
        ]);
    }
}
