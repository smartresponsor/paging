<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Guard\PageCanonGuardServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:canon:guard', description: 'Validates the Paging/Page naming and responsibility canon.')]
final class PageCanonGuardCommand extends Command
{
    public function __construct(private readonly PageCanonGuardServiceInterface $canonGuardService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Print the canon guard report as JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $report = $this->canonGuardService->buildReport();

        if ($input->getOption('json')) {
            $output->writeln((string) json_encode($report->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $report->passed() ? Command::SUCCESS : Command::FAILURE;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Paging/Page canon guard');
        $io->table(
            ['Code', 'Check', 'Status', 'Detail'],
            array_map(
                static fn ($item): array => [$item->code, $item->label, $item->passed ? 'passed' : 'failed', $item->detail],
                $report->items,
            ),
        );

        if (!$report->passed()) {
            $io->error(sprintf('Paging/Page canon guard failed: %d failed check(s).', $report->failedCount()));

            return Command::FAILURE;
        }

        $io->success(sprintf('Paging/Page canon guard passed: %d check(s).', $report->passedCount()));

        return Command::SUCCESS;
    }
}
