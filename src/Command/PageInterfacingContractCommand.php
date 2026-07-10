<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Interfacing\PageInterfacingContractServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'page:interfacing:contract', description: 'Reports Paging bridge requirements for Interfacing.')]
final class PageInterfacingContractCommand extends Command
{
    public function __construct(private readonly PageInterfacingContractServiceInterface $interfacingContractService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(json_encode($this->interfacingContractService->buildReport()->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
