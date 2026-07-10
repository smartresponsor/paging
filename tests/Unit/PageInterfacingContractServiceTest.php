<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Interfacing\PageInterfacingContractService;
use PHPUnit\Framework\TestCase;

final class PageInterfacingContractServiceTest extends TestCase
{
    public function testBuildReportDeclaresInterfacingBridgeSurface(): void
    {
        $report = (new PageInterfacingContractService())->buildReport();
        $payload = $report->toArray();
        self::assertTrue($report->passed());
        self::assertSame('paging', $payload['component']);
        self::assertSame('interfacing', $payload['consumer']);
        self::assertContains('bodyHtml', $payload['requiredPayloadFields']);
        self::assertContains('renderHints', $payload['requiredPayloadFields']);
        self::assertContains('preferredTemplateKey', $payload['requiredRenderHintFields']);
        self::assertContains('page_public_view', $payload['publicRoutes']);
        self::assertContains('@Interfacing/base.html.twig', $payload['templateContracts']);
    }
}
