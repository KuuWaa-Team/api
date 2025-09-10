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
        private readonly PaymentRepository $paymentRepository,
        private readonly OperationRepository $operationRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/admin/stats', name: 'app_admin_stats', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // === UTILISATEURS ===
        $totalUsers = $this->userRepository->count([]);

        $newUsersThisMonth = $this->userRepository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('FUNCTION("MONTH", u.createdAt) = FUNCTION("MONTH", CURRENT_DATE())')
            ->andWhere('FUNCTION("YEAR", u.createdAt) = FUNCTION("YEAR", CURRENT_DATE())')
            ->getQuery()
            ->getSingleScalarResult();


        $usersEvolution = $this->em->createQuery("
            SELECT FUNCTION('DATE_FORMAT', u.createdAt, '%Y-%m') as month, COUNT(u.id) as count
            FROM App\Entity\User u
            WHERE u.createdAt >= :date
            GROUP BY month
            ORDER BY month ASC
        ")->setParameter('date', new \DateTime('-12 months'))
            ->getResult();

        // === EVENTS ===
        $totalEvents = $this->eventRepository->count([]);
        $eventsThisMonth = $this->eventRepository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('FUNCTION("MONTH", e.createdAt) = FUNCTION("MONTH", CURRENT_DATE())')
            ->andWhere('FUNCTION("YEAR", e.createdAt) = FUNCTION("YEAR", CURRENT_DATE())')
            ->getQuery()
            ->getSingleScalarResult();

        $topEvents = $this->em->createQuery("
            SELECT e.name, COUNT(i.id) as invitations
            FROM App\Entity\Event e
            JOIN e.invitations i
            GROUP BY e.id
            ORDER BY invitations DESC
        ")->setMaxResults(5)->getResult();

        $eventsEvolution = $this->em->createQuery("
            SELECT FUNCTION('DATE_FORMAT', e.createdAt, '%Y-%m') as month, COUNT(e.id) as count
            FROM App\Entity\Event e
            WHERE e.createdAt >= :date
            GROUP BY month
            ORDER BY month ASC
        ")->setParameter('date', new \DateTime('-12 months'))
            ->getResult();

        // === PAYMENTS ===
        $totalAmount = $this->em->createQuery("
            SELECT SUM(p.amount) FROM App\Entity\Payment\Payment p
            WHERE p.status = :status
        ")->setParameter('status', 'SUCCESS')
            ->getSingleScalarResult() ?? 0;

        $failedAmount = $this->em->createQuery("
            SELECT SUM(p.amount) FROM App\Entity\Payment\Payment p
            WHERE p.status = :status
        ")->setParameter('status', 'FAILED')
            ->getSingleScalarResult() ?? 0;

        $amountThisMonth = $this->em->createQuery("
            SELECT SUM(p.amount) FROM App\Entity\Payment\Payment p
            WHERE p.status = :status
            AND MONTH(p.createdAt) = MONTH(CURRENT_DATE())
            AND YEAR(p.createdAt) = YEAR(CURRENT_DATE())
        ")->setParameter('status', 'SUCCESS')
            ->getSingleScalarResult() ?? 0;

        $transactions = $this->em->createQuery("
            SELECT 
                SUM(CASE WHEN p.status = 'SUCCESS' THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN p.status = 'FAILED' THEN 1 ELSE 0 END) as failed
            FROM App\Entity\Payment\Payment p
        ")->getSingleResult();

        $paymentsEvolution = $this->em->createQuery("
            SELECT FUNCTION('DATE_FORMAT', p.createdAt, '%Y-%m') as month, SUM(p.amount) as amount
            FROM App\Entity\Payment\Payment p
            WHERE p.status = 'SUCCESS'
            AND p.createdAt >= :date
            GROUP BY month
            ORDER BY month ASC
        ")->setParameter('date', new \DateTime('-12 months'))
            ->getResult();

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

        $activeRateThisMonth = $this->em->createQuery("
            SELECT COUNT(DISTINCT o.user) 
            FROM App\Entity\Wallet\Operation o
            WHERE MONTH(o.createdAt) = MONTH(CURRENT_DATE())
            AND YEAR(o.createdAt) = YEAR(CURRENT_DATE())
        ")->getSingleScalarResult();

        $activeRate = $totalUsers > 0 ? round(($activeRateThisMonth / $totalUsers) * 100, 2) . '%' : '0%';

        $engagementEvolution = $this->em->createQuery("
            SELECT FUNCTION('DATE_FORMAT', o.createdAt, '%Y-%m') as month, COUNT(DISTINCT o.user) as active_users
            FROM App\Entity\Wallet\Operation o
            WHERE o.createdAt >= :date
            GROUP BY month
            ORDER BY month ASC
        ")->setParameter('date', new \DateTime('-12 months'))
            ->getResult();

        // === PERFORMANCE ===
        $thisMonthPayments = $amountThisMonth;
        $lastMonthPayments = $this->em->createQuery("
            SELECT SUM(p.amount) FROM App\Entity\Payment\Payment p
            WHERE p.status = 'SUCCESS'
            AND MONTH(p.createdAt) = MONTH(DATE_SUB(CURRENT_DATE(), 1, 'MONTH'))
            AND YEAR(p.createdAt) = YEAR(DATE_SUB(CURRENT_DATE(), 1, 'MONTH'))
        ")->getSingleScalarResult() ?? 0;

        $progressionPayments = $lastMonthPayments > 0 
            ? round((($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100, 2) . '%' 
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
