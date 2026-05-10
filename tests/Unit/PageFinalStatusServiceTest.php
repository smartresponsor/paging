<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Finalization\PageFinalStatusService;
use PHPUnit\Framework\TestCase;

final class PageFinalStatusServiceTest extends TestCase
{
    public function testFinalStatusReportPassesForRcSurface(): void
    {
        $report = (new PageFinalStatusService())->buildReport();

        self::assertTrue($report->passed());
        self::assertSame(0, $report->failedCount());
        self::assertGreaterThanOrEqual(8, $report->passedCount());
        self::assertSame('Paging', $report->toArray()['component']);
        self::assertSame('Page', $report->toArray()['business_prefix']);
        self::assertSame('page_', $report->toArray()['database_prefix']);
        self::assertSame('page', $report->toArray()['config_prefix']);
    }
}
