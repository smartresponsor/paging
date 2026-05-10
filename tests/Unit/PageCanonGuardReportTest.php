<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Guard\PageCanonGuardItem;
use App\Paging\DTO\Guard\PageCanonGuardReport;
use PHPUnit\Framework\TestCase;

final class PageCanonGuardReportTest extends TestCase
{
    public function testReportCountsPassedAndFailedItems(): void
    {
        $report = new PageCanonGuardReport([
            new PageCanonGuardItem('a', 'A', true, 'ok'),
            new PageCanonGuardItem('b', 'B', false, 'no'),
        ]);

        self::assertFalse($report->passed());
        self::assertSame(1, $report->passedCount());
        self::assertSame(1, $report->failedCount());
        self::assertFalse($report->toArray()['passed']);
    }
}
