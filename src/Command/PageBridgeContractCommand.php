<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\ProviderInterface\Bridge\PageBridgeContractProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:bridge:contract', description: 'Prints the Paging/Page bridge contract for Interfacing consumers.')]
final class PageBridgeContractCommand extends Command
{
    public function __construct(private readonly PageBridgeContractProviderInterface $bridgeContractProvider)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Print the bridge contract summary as JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contract = [
            'component' => 'Paging',
            'namespace' => 'App\\Paging',
            'businessStem' => 'Page',
            'provider' => $this->bridgeContractProvider::class,
            'consumerBoundary' => 'Interfacing consumes PageBridgePayload/PageBridgeContractProvider instead of Doctrine Page entities.',
            'entrypoints' => [
                'byCode(string $code): PageBridgePayload',
                'bySlug(string $slug): PageBridgePayload',
                'forPublishedPage(Page $page): PageBridgePayload',
            ],
            'payloadFields' => [
                'code',
                'slug',
                'title',
                'kind',
                'status',
                'revisionNumber',
                'bodyHtml',
                'bodyText',
                'bodyMarkdown',
                'bodyJson',
                'checksum',
                'attachments',
                'renderHints',
                'legalNotice',
                'publishedAt',
                'effectiveFrom',
                'expiresAt',
                'updatedAt',
            ],
        ];

        if ($input->getOption('json')) {
            $output->writeln((string) json_encode($contract, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Paging/Page bridge contract');
        $io->definitionList(
            ['Component' => $contract['component']],
            ['Namespace' => $contract['namespace']],
            ['Business stem' => $contract['businessStem']],
            ['Provider' => $contract['provider']],
            ['Boundary' => $contract['consumerBoundary']],
        );
        $io->section('Entrypoints');
        $io->listing($contract['entrypoints']);
        $io->section('Payload fields');
        $io->listing($contract['payloadFields']);
        $io->success('Bridge contract is declared and available for Interfacing consumers.');

        return Command::SUCCESS;
    }
}
