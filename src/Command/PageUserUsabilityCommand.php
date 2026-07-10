<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Usability\PageUserUsabilityServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'page:user-usability:check', description: 'Reports Paging readiness for full host user/admin usage.')]
final class PageUserUsabilityCommand extends Command
{
    public function __construct(private readonly PageUserUsabilityServiceInterface $pageUserUsabilityService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Return machine-readable JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $report = $this->pageUserUsabilityService->buildReport();

        if (true === $input->getOption('json')) {
            $output->writeln(json_encode($report->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return $report->componentReady() ? Command::SUCCESS : Command::FAILURE;
        }

        $output->writeln(sprintf('Paging user usability surface: %s', $report->componentReady() ? 'ready' : 'incomplete'));
        foreach ($report->items as $item) {
            $output->writeln(sprintf(
                '- [%s] %s (%s): %s',
                $item->componentReady ? 'ok' : 'missing',
                $item->code,
                $item->ownerLayer,
                $item->detail,
            ));
        }

        return $report->componentReady() ? Command::SUCCESS : Command::FAILURE;
    }
}
