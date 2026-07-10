<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Workflow\PageWorkflowAcceptanceServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'page:workflow:acceptance', description: 'Reports full Paging host workflow acceptance readiness.')]
final class PageWorkflowAcceptanceCommand extends Command
{
    public function __construct(private readonly PageWorkflowAcceptanceServiceInterface $workflowAcceptanceService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(json_encode($this->workflowAcceptanceService->buildReport()->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
