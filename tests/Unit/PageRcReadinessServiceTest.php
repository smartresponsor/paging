<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Readiness\PageRcReadinessService;
use PHPUnit\Framework\TestCase;

final class PageRcReadinessServiceTest extends TestCase
{
    public function testRcReadinessReportPassesForCurrentComponentSurface(): void
    {
        $report = (new PageRcReadinessService())->buildReport();

        self::assertTrue($report->passed());
        self::assertSame(0, $report->failedCount());
        self::assertGreaterThanOrEqual(6, $report->passedCount());
        self::assertContains('user_usability', array_column($report->toArray()['items'], 'code'));
        self::assertArrayHasKey('items', $report->toArray());
    }
}
