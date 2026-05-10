<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\Repository\PageRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'page:audit:readiness', description: 'Audits the Paging component runtime readiness baseline.')]
final class PageAuditReadinessCommand extends Command
{
    public function __construct(private readonly PageRepository $pageRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pages = $this->pageRepository->findAll();
        $published = 0;
        $drafts = 0;
        $missingRevision = 0;

        foreach ($pages as $page) {
            if (null !== $page->getPublishedRevision()) {
                ++$published;
            }
            if (null === $page->getCurrentRevision()) {
                ++$missingRevision;
            }
            if ('draft' === $page->getStatus()->value) {
                ++$drafts;
            }
        }

        $io->title('Paging readiness audit');
        $io->definitionList(
            ['pages' => (string) count($pages)],
            ['published' => (string) $published],
            ['drafts' => (string) $drafts],
            ['without current revision' => (string) $missingRevision],
        );

        if ($missingRevision > 0) {
            $io->warning('Some pages have no current revision. Create revisions before publishing.');
        } else {
            $io->success('Paging readiness audit completed.');
        }

        return Command::SUCCESS;
    }
}
