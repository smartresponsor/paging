<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Readiness\PageRcReadinessServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:rc:readiness', description: 'Prints the Paging release-candidate readiness checklist.')]
final class PageRcReadinessCommand extends Command
{
    public function __construct(private readonly PageRcReadinessServiceInterface $readinessService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $report = $this->readinessService->buildReport();

        $io->title('Paging RC readiness');
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
            $io->error(sprintf('Paging RC readiness failed: %d failed check(s).', $report->failedCount()));

            return Command::FAILURE;
        }

        $io->success(sprintf('Paging RC readiness passed: %d check(s).', $report->passedCount()));

        return Command::SUCCESS;
    }
}
