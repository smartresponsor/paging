<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Guard\PageCanonGuardItemDTO;
use App\Paging\DTO\Guard\PageCanonGuardReportDTO;
use PHPUnit\Framework\TestCase;

final class PageCanonGuardReportTest extends TestCase
{
    public function testReportCountsPassedAndFailedItems(): void
    {
        $report = new PageCanonGuardReportDTO([
            new PageCanonGuardItemDTO('a', 'A', true, 'ok'),
            new PageCanonGuardItemDTO('b', 'B', false, 'no'),
        ]);

        self::assertFalse($report->passed());
        self::assertSame(1, $report->passedCount());
        self::assertSame(1, $report->failedCount());
        self::assertFalse($report->toArray()['passed']);
    }
}
