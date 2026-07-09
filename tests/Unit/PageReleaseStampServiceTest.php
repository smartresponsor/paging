<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Completion\PageCompletionReport;
use App\Paging\DTO\Guard\PageCanonGuardReport;
use App\Paging\DTO\Handoff\PageHandoffReport;
use App\Paging\Service\Release\PageReleaseStampService;
use App\Paging\ServiceInterface\Completion\PageCompletionServiceInterface;
use App\Paging\ServiceInterface\Guard\PageCanonGuardServiceInterface;
use App\Paging\ServiceInterface\Handoff\PageHandoffSummaryServiceInterface;
use PHPUnit\Framework\TestCase;

final class PageReleaseStampServiceTest extends TestCase
{
    public function testReleaseStampReportKeepsPageCanonMetadata(): void
    {
        $service = new PageReleaseStampService(
            new class implements PageCompletionServiceInterface {
                public function buildReport(): PageCompletionReport
                {
                    return new PageCompletionReport([]);
                }
            },
            new class implements PageCanonGuardServiceInterface {
                public function buildReport(): PageCanonGuardReport
                {
                    return new PageCanonGuardReport([]);
                }
            },
            new class implements PageHandoffSummaryServiceInterface {
                public function buildReport(): PageHandoffReport
                {
                    return new PageHandoffReport([]);
                }
            },
        );

        $report = $service->buildReport()->toArray();

        self::assertSame('Paging', $report['component']);
        self::assertSame('App\\Paging', $report['namespace']);
        self::assertSame('Page', $report['businessPrefix']);
        self::assertSame('page', $report['configRoot']);
        self::assertSame('page_', $report['tablePrefix']);
        self::assertTrue($report['passed']);
    }
}
