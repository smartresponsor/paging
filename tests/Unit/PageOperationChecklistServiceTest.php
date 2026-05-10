<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Operations\PageOperationChecklistService;
use PHPUnit\Framework\TestCase;

final class PageOperationChecklistServiceTest extends TestCase
{
    public function testBuildReportContainsPostRcOperationsSurface(): void
    {
        $report = (new PageOperationChecklistService())->buildReport();

        self::assertTrue($report->passed(), 'The operational checklist should pass in the component source tree.');
        self::assertGreaterThanOrEqual(5, $report->passedCount());
        self::assertSame(0, $report->failedCount());
    }
}
