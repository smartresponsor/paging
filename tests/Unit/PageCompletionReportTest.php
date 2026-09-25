<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Completion\PageCompletionItemDTO;
use App\Paging\DTO\Completion\PageCompletionReportDTO;
use PHPUnit\Framework\TestCase;

final class PageCompletionReportTest extends TestCase
{
    public function testCompletionReportCountsPassedAndFailedItems(): void
    {
        $report = new PageCompletionReportDTO([
            new PageCompletionItemDTO('a', 'A', true, 'passed'),
            new PageCompletionItemDTO('b', 'B', false, 'failed'),
        ]);

        self::assertFalse($report->passed());
        self::assertSame(1, $report->passedCount());
        self::assertSame(1, $report->failedCount());
        self::assertSame('Paging', $report->toArray()['component']);
        self::assertSame('Page', $report->toArray()['businessPrefix']);
    }
}
