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
        self::assertGreaterThanOrEqual(5, $report->passedCount());
        self::assertArrayHasKey('items', $report->toArray());
    }
}
