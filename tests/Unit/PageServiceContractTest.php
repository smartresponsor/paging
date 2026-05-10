<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Revision\PageRevisionCreateInput;
use App\Paging\Entity\Page;
use App\Paging\Enum\PageGrantType;
use App\Paging\Service\Rendering\PageRenderService;
use App\Paging\Service\Revision\PageRevisionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class PageServiceContractTest extends TestCase
{
    public function testRenderPublishedPageRequiresPublishedRevision(): void
    {
        $page = new Page('privacy_policy', 'privacy-policy', 'Privacy Policy');

        $this->expectException(\RuntimeException::class);
        (new PageRenderService())->renderPublished($page);
    }

    public function testRevisionServiceCreatesTextFallbackAndCurrentRevision(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('persist');
        $entityManager->expects(self::once())->method('flush');

        $page = new Page('terms', 'terms', 'Terms');
        $service = new PageRevisionService($entityManager);
        $revision = $service->createRevision($page, new PageRevisionCreateInput('Terms', '<h1>Terms</h1><p>Hello</p>'));

        self::assertSame(1, $revision->getRevisionNumber());
        self::assertSame('TermsHello', $revision->getBodyText());
        self::assertSame($revision, $page->getCurrentRevision());
    }

    public function testGrantEnumContainsPageLevelManage(): void
    {
        self::assertSame('manage', PageGrantType::Manage->value);
    }
}
