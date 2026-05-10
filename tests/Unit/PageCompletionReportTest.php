<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Completion\PageCompletionItem;
use App\Paging\DTO\Completion\PageCompletionReport;
use PHPUnit\Framework\TestCase;

final class PageCompletionReportTest extends TestCase
{
    public function testCompletionReportCountsPassedAndFailedItems(): void
    {
        $report = new PageCompletionReport([
            new PageCompletionItem('a', 'A', true, 'passed'),
            new PageCompletionItem('b', 'B', false, 'failed'),
        ]);

        self::assertFalse($report->passed());
        self::assertSame(1, $report->passedCount());
        self::assertSame(1, $report->failedCount());
        self::assertSame('Paging', $report->toArray()['component']);
        self::assertSame('Page', $report->toArray()['businessPrefix']);
    }
}
