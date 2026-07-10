<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Navigation\PageNavigationContractServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'page:navigation:contract', description: 'Reports Paging navigation entries for host/Navigating import.')]
final class PageNavigationContractCommand extends Command
{
    public function __construct(private readonly PageNavigationContractServiceInterface $navigationContractService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $payload = $this->navigationContractService->buildReport()->toArray();
        $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
