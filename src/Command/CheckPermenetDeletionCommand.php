<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-permanent-deletion',
    description: 'Supprime définitivement les comptes marqués supprimés depuis plus de 10 jours',
)]
class CheckPermenetDeletionCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $now = new \DateTimeImmutable();
        $deletedUsers = $this->userRepository->findUsersMarkedForDeletion();

        $count = 0;

        foreach ($deletedUsers as $user) {
            $deletedAt = $user->getDeletedAt();

            if ($deletedAt && $now->diff($deletedAt)->days >= 10) {
                $this->entityManager->remove($user);
                $count++;
            }
        }

        if ($count > 0) {
            $this->entityManager->flush();
        }

        $io->success("Suppression définitive terminée. $count utilisateur(s) supprimé(s).");

        return Command::SUCCESS;
    }
}
