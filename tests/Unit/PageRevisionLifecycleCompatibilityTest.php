<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use PHPUnit\Framework\TestCase;

final class PageRevisionLifecycleCompatibilityTest extends TestCase
{
    public function testLockLifecycleAliasesAreAvailable(): void
    {
        $page = $this->createPage();
        $revision = new PageRevision($page, 1, 'Title', '<p>Body</p>', 'Body', null, null, null, 'creator-1');

        self::assertInstanceOf(\DateTimeImmutable::class, $revision->createdAt());
        self::assertNull($revision->lockedAt());
        self::assertNull($revision->lockedBy());

        $revision->lock('locker-1', new \DateTimeImmutable('2026-05-23 12:05:00'));

        self::assertSame('locker-1', $revision->lockedBy());
        self::assertSame('2026-05-23 12:05:00', $revision->lockedAt()?->format('Y-m-d H:i:s'));
        self::assertTrue($revision->isLocked());

        $revision->unlock();

        self::assertNull($revision->lockedAt());
        self::assertNull($revision->lockedBy());
        self::assertFalse($revision->isLocked());
    }

    private function createPage(): Page
    {
        return new Page('page-1', 'page-1', 'Page');
    }
}
