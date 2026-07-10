<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Usability\PageUserUsabilityService;
use PHPUnit\Framework\TestCase;

final class PageUserUsabilityServiceTest extends TestCase
{
    public function testBuildReportDeclaresFullHostUserUsageSurface(): void
    {
        $report = (new PageUserUsabilityService())->buildReport();
        $payload = $report->toArray();

        self::assertTrue($report->componentReady());
        self::assertSame(0, $report->pendingCount());
        self::assertGreaterThanOrEqual(6, $report->readyCount());
        self::assertContains('Backofficing/EasyAdmin', $report->ownerLayers());
        self::assertContains('Viewing/Interfacing', $report->ownerLayers());
        self::assertContains('Host security', $report->ownerLayers());
        self::assertTrue($payload['componentReady']);
    }

    public function testServiceDrivenAdminActionsAreExplicit(): void
    {
        $items = (new PageUserUsabilityService())->buildReport()->items;
        $codes = array_map(static fn ($item): string => $item->code, $items);

        self::assertContains('backofficing_forms', $codes);
        self::assertContains('service_driven_admin_actions', $codes);
    }
}
