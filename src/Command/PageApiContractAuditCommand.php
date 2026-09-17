<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ServiceInterface\Api\PageApiContractServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:api:contract', description: 'Prints the stable Paging Page API/output contract.')]
final class PageApiContractAuditCommand extends Command
{
    public function __construct(private readonly PageApiContractServiceInterface $contractService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $report = $this->contractService->buildReport();

        $io->title('Paging API contract');
        $io->table(
            ['Name', 'Method', 'Path', 'Formats', 'Write', 'Published revision', 'Purpose'],
            array_map(
                static fn ($endpoint): array => [
                    $endpoint->nameEntity,
                    $endpoint->method,
                    $endpoint->path,
                    implode(', ', $endpoint->formats),
                    $endpoint->writesState ? 'yes' : 'no',
                    $endpoint->requiresPublishedRevision ? 'yes' : 'no',
                    $endpoint->purpose,
                ],
                $report->endpoints,
            ),
        );

        $io->success(sprintf('Paging API contract contains %d endpoint(s).', $report->endpointCount()));

        return Command::SUCCESS;
    }
}
