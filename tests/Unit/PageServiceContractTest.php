<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Authoring\PageUpdateInputDTO;
use App\Paging\DTO\Revision\PageRevisionCreateInputDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Enum\PageGrantType;
use App\Paging\RepositoryInterface\PageRepositoryInterface;
use App\Paging\RepositoryInterface\PageRevisionRepositoryInterface;
use App\Paging\Service\Authoring\PageDraftService;
use App\Paging\Service\Rendering\PageRenderService;
use App\Paging\Service\Revision\PageRevisionService;
use App\Paging\ValueObject\PageSlug;
use PHPUnit\Framework\TestCase;

final class PageServiceContractTest extends TestCase
{
    public function testRenderPublishedPageRequiresPublishedRevision(): void
    {
        $page = new Page('privacy_policy', PageSlug::fromSource('privacy_policy')->value(), 'Privacy Policy');

        $this->expectException(\RuntimeException::class);
        (new PageRenderService())->renderPublished($page);
    }

    public function testRevisionServiceCreatesTextFallbackAndCurrentRevision(): void
    {
        $repository = $this->createMock(PageRevisionRepositoryInterface::class);
        $repository->expects(self::once())->method('save');

        $page = new Page('terms', PageSlug::fromSource('terms')->value(), 'Terms');
        $service = new PageRevisionService($repository);
        $revision = $service->createRevision($page, new PageRevisionCreateInputDTO('Terms', '<h1>Terms</h1><p>Hello</p>'));

        self::assertSame(1, $revision->getRevisionNumber());
        self::assertSame('TermsHello', $revision->getBodyText());
        self::assertSame($revision, $page->getCurrentRevision());
    }

    public function testDraftServiceKeepsExistingSlugWhenUpdateSlugIsBlank(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->expects(self::once())->method('flush');
        $page = new Page('about', 'about-us', 'About');

        $updated = (new PageDraftService($repository))->updatePage(
            $page,
            new PageUpdateInputDTO('About updated', '   ', 'owner-2'),
        );

        self::assertSame('about-us', $updated->getSlug());
        self::assertSame('About updated', $updated->getTitle());
        self::assertSame('owner-2', $updated->getOwnerUserId());
    }

    public function testRenderRevisionSelectsMatchingPublicationAfterEarlierNonMatchingEntry(): void
    {
        $page = new Page('policy', 'policy', 'Policy');
        $first = new PageRevision($page, 1, 'Policy v1', '<p>One</p>', 'One');
        $second = new PageRevision($page, 2, 'Policy v2', '<p>Two</p>', 'Two');
        $page->getPublications()->add(new PagePublication($page, $first, new \DateTimeImmutable('2026-09-01T00:00:00+00:00')));
        $secondPublication = new PagePublication($page, $second, new \DateTimeImmutable('2026-09-02T00:00:00+00:00'));
        $page->getPublications()->add($secondPublication);

        $view = (new PageRenderService())->renderRevision($second);

        self::assertSame(2, $view->version);
        self::assertSame($secondPublication->getPublishedAt(), $view->publishedAt);
    }

    public function testGrantEnumContainsPageLevelManage(): void
    {
        self::assertSame('manage', PageGrantType::Manage->value);
    }
}
