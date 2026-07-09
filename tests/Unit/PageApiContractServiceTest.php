<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Contract\PageApiContractService;
use PHPUnit\Framework\TestCase;

final class PageApiContractServiceTest extends TestCase
{
    public function testItReportsStablePageApiContract(): void
    {
        $report = (new PageApiContractService())->buildReport();

        self::assertGreaterThanOrEqual(10, $report->endpointCount());

        $names = array_map(static fn ($endpoint): string => $endpoint->nameEntity, $report->endpoints);

        self::assertContains('page_public_view', $names);
        self::assertContains('page_public_index', $names);
        self::assertContains('page_api_bridge', $names);
        self::assertContains('page_export_json', $names);
        self::assertContains('page_acceptance_create', $names);
        self::assertContains('page_acceptance_check', $names);

        $payload = $report->toArray();
        self::assertSame('GET', $payload[0]['method']);
        self::assertSame('/page/', $payload[0]['path']);
    }
}
