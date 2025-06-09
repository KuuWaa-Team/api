<?php

namespace App\Command;

use App\Repository\Wallet\OperationRepository;
use App\Service\WalletService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'payment:check-status',
    description: 'Add a short description for your command',
)]
class PaymentCheckStatusCommand extends Command
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly WalletService $walletService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $operations = $this->operationRepository->findByStatus(WalletService::$STATUS_WAITING);

        foreach ($operations as $operation) {
            $this->walletService->checkPaymentStatus($operation);
        }

        $io->success('Success.');

        return Command::SUCCESS;
    }
}
