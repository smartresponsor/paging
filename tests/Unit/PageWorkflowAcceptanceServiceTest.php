<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Workflow\PageWorkflowAcceptanceService;
use PHPUnit\Framework\TestCase;

final class PageWorkflowAcceptanceServiceTest extends TestCase
{
    public function testBuildReportDeclaresFullUserWorkflow(): void
    {
        $report = (new PageWorkflowAcceptanceService())->buildReport();
        $payload = $report->toArray();
        $codes = array_map(static fn (array $step): string => $step['code'], $payload['steps']);
        self::assertTrue($report->passed());
        self::assertSame(8, $report->stepCount());
        self::assertSame('paging', $payload['component']);
        self::assertSame('create_page_create_revision_publish_view_bridge_navigation_security', $payload['scenario']);
        self::assertContains('create_page', $codes);
        self::assertContains('publish_revision', $codes);
        self::assertContains('interfacing_bridge', $codes);
        self::assertContains('navigating_entrypoints', $codes);
        self::assertContains('security_access', $codes);
    }
}
