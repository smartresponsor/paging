<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Runtime\PageRuntimeProbeServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:debug:container', description: 'Prints the Paging runtime container baseline.')]
final class PageDebugContainerCommand extends Command
{
    public function __construct(private readonly PageRuntimeProbeServiceInterface $runtimeProbeService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Paging runtime container baseline');
        $io->definitionList(...array_map(
            static fn (string $key, mixed $value): array => [$key => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value],
            array_keys($this->runtimeProbeService->snapshot()),
            $this->runtimeProbeService->snapshot(),
        ));

        return Command::SUCCESS;
    }
}
