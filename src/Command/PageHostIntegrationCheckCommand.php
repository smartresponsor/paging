<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Runtime\PageRuntimeProbeServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'page:host:integration-check', description: 'Checks Paging standalone and host-application integration prerequisites.')]
final class PageHostIntegrationCheckCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly PageRuntimeProbeServiceInterface $runtimeProbeService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $projectDir = $this->kernel->getProjectDir();
        $snapshot = $this->runtimeProbeService->snapshot();

        $checks = [
            $this->fileCheck($projectDir, 'composer.json', 'Composer package metadata'),
            $this->fileCheck($projectDir, 'config/packages/page_config.yaml', 'Page configuration'),
            $this->fileCheck($projectDir, 'config/routes.yaml', 'YAML route loader'),
            $this->fileCheck($projectDir, 'src/PageBundle.php', 'Bundle entrypoint'),
            $this->fileCheck($projectDir, 'src/DependencyInjection/PageExtension.php', 'DI extension'),
            $this->fileCheck($projectDir, 'src/Kernel.php', 'Standalone runtime kernel'),
        ];

        $checks[] = [
            'Runtime namespace',
            'App\\Paging' === $snapshot['namespace'] ? 'passed' : 'failed',
            (string) $snapshot['namespace'],
        ];
        $checks[] = [
            'Business prefix',
            'Page' === $snapshot['business_prefix'] ? 'passed' : 'failed',
            (string) $snapshot['business_prefix'],
        ];
        $checks[] = [
            'Database prefix',
            'page_' === $snapshot['database_prefix'] ? 'passed' : 'failed',
            (string) $snapshot['database_prefix'],
        ];
        $checks[] = [
            'Config prefix',
            'page' === $snapshot['config_prefix'] ? 'passed' : 'failed',
            (string) $snapshot['config_prefix'],
        ];

        $io->title('Paging host integration check');
        $io->table(['Check', 'Status', 'Detail'], $checks);

        foreach ($checks as $check) {
            if ('passed' !== $check[1]) {
                $io->error('Paging host integration check failed.');

                return Command::FAILURE;
            }
        }

        $io->success('Paging host integration check passed.');

        return Command::SUCCESS;
    }

    /** @return array{0: string, 1: string, 2: string} */
    private function fileCheck(string $projectDir, string $relativePath, string $label): array
    {
        $path = $projectDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        return [
            $label,
            is_file($path) ? 'passed' : 'failed',
            $relativePath,
        ];
    }
}
