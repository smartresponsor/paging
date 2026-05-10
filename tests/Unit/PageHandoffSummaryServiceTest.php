<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Handoff\PageHandoffSummaryService;
use PHPUnit\Framework\TestCase;

final class PageHandoffSummaryServiceTest extends TestCase
{
    public function testHandoffSummaryKeepsPagingBoundaryReady(): void
    {
        $report = (new PageHandoffSummaryService())->buildReport();

        self::assertSame('ready', $report->status());
        self::assertSame('Paging', $report->toArray()['component']);
        self::assertSame('Page', $report->toArray()['business_stem']);
        self::assertSame('page_', $report->toArray()['database_prefix']);
        self::assertContains('Backofficing for EasyAdmin/operator CRUD surfaces', $report->toArray()['next_owner_layers']);
    }
}
