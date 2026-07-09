<?php

declare(strict_types=1);

namespace App\Paging\Command;

use App\Paging\DTO\Authoring\PageCreateInput;
use App\Paging\DTO\Publication\PagePublishInput;
use App\Paging\DTO\Revision\PageRevisionCreateInput;
use App\Paging\Enum\PageKind;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;
use App\Paging\ValueObject\PageSlug;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'page:seed:demo', description: 'Seed minimal demo pages for standalone Paging debugging.')]
final class PageSeedDemoCommand extends Command
{
    public function __construct(
        private readonly PageRepository $pages,
        private readonly PageDraftServiceInterface $drafts,
        private readonly PageRevisionServiceInterface $revisions,
        private readonly PagePublicationServiceInterface $publications,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->demoPages() as $demo) {
            if (null !== $this->pages->findOneBy(['code' => $demo['code']])) {
                $output->writeln(sprintf('Skipped existing page: %s', $demo['code']));
                continue;
            }

            $page = $this->drafts->createPage(new PageCreateInput(
                code: $demo['code'],
                slug: $demo['slug'],
                title: $demo['title'],
                kind: $demo['kind'],
                ownerUserId: 'demo-owner',
            ));

            $revision = $this->revisions->createRevision($page, new PageRevisionCreateInput(
                title: $demo['title'],
                bodyHtml: $demo['bodyHtml'],
                bodyText: null,
                bodyMarkdown: $demo['bodyMarkdown'],
                changeNote: 'Initial demo revision.',
                createdByUserId: 'demo-owner',
            ));

            $this->publications->publishRevision($revision, new PagePublishInput(publishedByUserId: 'demo-owner'));
            $output->writeln(sprintf('Seeded page: %s', $demo['code']));
        }

        return Command::SUCCESS;
    }

    /** @return list<array{code:string, slug:string, title:string, kind:PageKind, bodyHtml:string, bodyMarkdown:string}> */
    private function demoPages(): array
    {
        return [
            [
                'code' => 'privacy_policy',
                'slug' => PageSlug::fromSource('privacy-policy')->value(),
                'title' => 'Privacy Policy',
                'kind' => PageKind::Policy,
                'bodyHtml' => '<h1>Privacy Policy</h1><p>This demo policy is managed by Paging revisions.</p>',
                'bodyMarkdown' => "# Privacy Policy\n\nThis demo policy is managed by Paging revisions.",
            ],
            [
                'code' => 'terms_of_service',
                'slug' => PageSlug::fromSource('terms-of-service')->value(),
                'title' => 'Terms of Service',
                'kind' => PageKind::Policy,
                'bodyHtml' => '<h1>Terms of Service</h1><p>This demo terms page is published from a locked revision.</p>',
                'bodyMarkdown' => "# Terms of Service\n\nThis demo terms page is published from a locked revision.",
            ],
            [
                'code' => 'about',
                'slug' => PageSlug::fromSource('about')->value(),
                'title' => 'About',
                'kind' => PageKind::Page,
                'bodyHtml' => '<h1>About</h1><p>A simple public page rendered by the Paging component.</p>',
                'bodyMarkdown' => "# About\n\nA simple public page rendered by the Paging component.",
            ],
            [
                'code' => 'sample_blog',
                'slug' => PageSlug::fromSource('sample-blog')->value(),
                'title' => 'Sample Blog',
                'kind' => PageKind::Blog,
                'bodyHtml' => '<h1>Sample Blog</h1><p>A minimal blog-shaped page using the same Page model.</p>',
                'bodyMarkdown' => "# Sample Blog\n\nA minimal blog-shaped page using the same Page model.",
            ],
        ];
    }
}
