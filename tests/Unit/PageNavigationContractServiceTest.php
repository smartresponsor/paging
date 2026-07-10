<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Navigation\PageNavigationContractService;
use PHPUnit\Framework\TestCase;

final class PageNavigationContractServiceTest extends TestCase
{
    public function testBuildReportExposesAdminNavigationEntries(): void
    {
        $report = (new PageNavigationContractService())->buildReport();
        $payload = $report->toArray();
        self::assertSame(5, $report->itemCount());
        self::assertContains('page_admin_page_index', $report->routeNames());
        self::assertContains('page_admin_page_revision_index', $report->routeNames());
        self::assertSame('paging', $payload['component']);
        self::assertSame('shell.left.bottom', $payload['location']);
        self::assertCount(5, $payload['navigatingEntitySeeds']);
    }

    public function testItemsAreAdminOnlyAndNavigatingCompatible(): void
    {
        $items = (new PageNavigationContractService())->buildReport()->toArray()['items'];
        foreach ($items as $item) {
            self::assertSame('link', $item['type']);
            self::assertSame('shell.left.bottom', $item['location']);
            self::assertContains('ROLE_ADMIN', $item['visible_for_roles']);
            self::assertTrue($item['metadata']['admin_only']);
            self::assertTrue($item['metadata']['easyadmin_operator_surface']);
            self::assertSame('App\\Paging\\Controller\\Admin', $item['metadata']['namespace_provider']);
            self::assertStringStartsWith('/admin/page', $item['path']);
        }
    }
}
