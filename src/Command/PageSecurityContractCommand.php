<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Security\PageSecurityContractServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'page:security:contract', description: 'Reports Paging host security integration contract.')]
final class PageSecurityContractCommand extends Command
{
    public function __construct(private readonly PageSecurityContractServiceInterface $securityContractService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(json_encode($this->securityContractService->buildReport()->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
