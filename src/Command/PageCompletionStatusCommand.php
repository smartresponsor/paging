<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Completion\PageCompletionServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:completion:status', description: 'Prints the final Paging/Page completion status for RC handoff.')]
final class PageCompletionStatusCommand extends Command
{
    public function __construct(private readonly PageCompletionServiceInterface $completionService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Print the completion report as JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $report = $this->completionService->buildReport();

        if ($input->getOption('json')) {
            $output->writeln((string) json_encode($report->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $report->passed() ? Command::SUCCESS : Command::FAILURE;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Paging/Page completion status');
        $io->table(
            ['Code', 'Check', 'Status', 'Detail'],
            array_map(
                static fn ($item): array => [$item->code, $item->label, $item->passed ? 'passed' : 'failed', $item->detail],
                $report->items,
            ),
        );

        if (!$report->passed()) {
            $io->error(sprintf('Paging/Page completion status failed: %d failed check(s).', $report->failedCount()));

            return Command::FAILURE;
        }

        $io->success(sprintf('Paging/Page completion status passed: %d check(s).', $report->passedCount()));

        return Command::SUCCESS;
    }
}
