<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class CreditService
{
    private float $creditPrice = 1 / 1.2;

    public function __construct(
        private readonly UserRepository $userRepository
    )
    {}

    public function getCreditPrice(): float
    {
        return $this->creditPrice;
    }

    public function rechargeUser(User $user, int $amount): void
    {
        $currentBalance = $user->getCredits() ?? 0;
        $newBalance = $currentBalance + $amount;
        $user->setCredits($newBalance);
        $this->userRepository->save($user);
    }

    public function reduce(User $user, int $amount): void
    {
        $currentBalance = $user->getCredits();
        $newBalance = $currentBalance - $amount;
        $user->setCredits($newBalance);
        $this->userRepository->save($user);
    }
}

// 1 SMS = 0,099 USD