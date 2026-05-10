<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\ValueObject\PageChecksum;
use App\Paging\ValueObject\PageCode;
use App\Paging\ValueObject\PageRevisionNumber;
use App\Paging\ValueObject\PageSlug;
use PHPUnit\Framework\TestCase;

final class PageValueObjectTest extends TestCase
{
    public function testPageCodeAcceptsStableBusinessIdentifier(): void
    {
        self::assertSame('privacy_policy', (new PageCode('privacy_policy'))->value());
    }

    public function testPageSlugTrimsSlashes(): void
    {
        self::assertSame('legal/privacy-policy', (new PageSlug('/legal/privacy-policy/'))->value());
    }

    public function testChecksumIsDeterministic(): void
    {
        self::assertSame(
            PageChecksum::fromContent('title', 'body')->value(),
            PageChecksum::fromContent('title', 'body')->value(),
        );
    }

    public function testRevisionNumberStartsAtOne(): void
    {
        self::assertSame(1, (new PageRevisionNumber(1))->value());
    }
}
