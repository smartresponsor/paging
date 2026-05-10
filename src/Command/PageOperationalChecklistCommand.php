<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Operations\PageOperationChecklistServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:operations:check', description: 'Prints the Paging post-RC operational handoff checklist.')]
final class PageOperationalChecklistCommand extends Command
{
    public function __construct(private readonly PageOperationChecklistServiceInterface $operationChecklistService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $report = $this->operationChecklistService->buildReport();

        $io->title('Paging operational handoff checklist');
        $io->table(
            ['Code', 'Check', 'Status', 'Detail'],
            array_map(
                static fn ($item): array => [
                    $item->code,
                    $item->label,
                    $item->passed ? 'passed' : 'failed',
                    $item->detail,
                ],
                $report->items,
            ),
        );

        if (!$report->passed()) {
            $io->error(sprintf('Paging operational handoff failed: %d failed check(s).', $report->failedCount()));

            return Command::FAILURE;
        }

        $io->success(sprintf('Paging operational handoff passed: %d check(s).', $report->passedCount()));

        return Command::SUCCESS;
    }
}
