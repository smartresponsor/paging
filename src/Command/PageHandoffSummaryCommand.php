<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Handoff\PageHandoffSummaryServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:handoff:summary', description: 'Prints the Paging post-RC handoff summary.')]
final class PageHandoffSummaryCommand extends Command
{
    public function __construct(private readonly PageHandoffSummaryServiceInterface $handoffSummaryService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Print the handoff summary as JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $report = $this->handoffSummaryService->buildReport();

        if ($input->getOption('json')) {
            $output->writeln((string) json_encode($report->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Paging post-RC handoff summary');
        $io->definitionList(
            ['component' => 'Paging'],
            ['namespace' => 'App\\Paging'],
            ['business stem' => 'Page'],
            ['database prefix' => 'page_'],
            ['config prefix' => 'page'],
            ['status' => $report->status()],
        );
        $io->table(
            ['Code', 'Status', 'Detail'],
            array_map(
                static fn ($item): array => [$item->code, $item->status, $item->detail],
                $report->items,
            ),
        );
        $io->success('Paging handoff summary is ready for Backofficing/Interfacing integration.');

        return Command::SUCCESS;
    }
}
