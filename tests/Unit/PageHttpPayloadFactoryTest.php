<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Rendering\PageRenderView;
use App\Paging\Enum\PageKind;
use App\Paging\Service\Http\PageHttpPayloadFactory;
use PHPUnit\Framework\TestCase;

final class PageHttpPayloadFactoryTest extends TestCase
{
    public function testRenderViewPayloadUsesScalarValuesForHttpBoundary(): void
    {
        $factory = new PageHttpPayloadFactory();
        $payload = $factory->renderViewToArray(new PageRenderView(
            'privacy_policy',
            'privacy-policy',
            'Privacy Policy',
            PageKind::Policy,
            2,
            '<p>Policy</p>',
            'Policy',
            '# Policy',
            str_repeat('a', 64),
            new \DateTimeImmutable('2026-05-04T10:00:00-05:00'),
            new \DateTimeImmutable('2026-05-10T00:00:00-05:00'),
        ));

        self::assertSame('policy', $payload['kind']);
        self::assertSame(2, $payload['version']);
        self::assertSame('2026-05-04T10:00:00-05:00', $payload['publishedAt']);
        self::assertSame('2026-05-10T00:00:00-05:00', $payload['effectiveFrom']);
    }
}
