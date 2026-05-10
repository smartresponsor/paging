<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Runtime\PageRuntimeProbeService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class PageRuntimeProbeServiceTest extends TestCase
{
    public function testSnapshotKeepsPagingNamespaceAndPageBusinessPrefixes(): void
    {
        $service = new PageRuntimeProbeService(new ParameterBag([
            'page.public_route_prefix' => '/page',
            'page.api_route_prefix' => '/api/page',
            'page.revision_lock_after_publish' => true,
            'page.standalone_runtime' => false,
        ]));

        $snapshot = $service->snapshot();

        self::assertSame('Paging', $snapshot['component']);
        self::assertSame('App\\Paging', $snapshot['namespace']);
        self::assertSame('Page', $snapshot['business_prefix']);
        self::assertSame('page_', $snapshot['database_prefix']);
        self::assertSame('page', $snapshot['config_prefix']);
        self::assertSame('/page', $snapshot['public_route_prefix']);
        self::assertSame('/api/page', $snapshot['api_route_prefix']);
    }
}
