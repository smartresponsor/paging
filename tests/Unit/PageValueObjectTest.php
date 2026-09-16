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
        self::assertSame('privacy_policy', (string) new PageCode(' privacy_policy '));
    }

    public function testPageCodeRejectsInvalidIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new PageCode('INVALID CODE');
    }

    public function testPageSlugNormalizesToStableSemanticValue(): void
    {
        $slug = new PageSlug('/legal/privacy-policy/');
        self::assertSame('legal-privacy-policy', $slug->value());
        self::assertSame($slug->value(), (new PageSlug('legal/privacy-policy'))->value());
        self::assertSame('legal-privacy-policy', (string) $slug);
        self::assertSame('source-value', PageSlug::fromSource('Source Value')->value());
        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', PageSlug::random()->value());
        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', (new PageSlug('---'))->value());
    }

    public function testChecksumIsDeterministic(): void
    {
        self::assertSame(
            PageChecksum::fromContent('title', 'body')->value(),
            PageChecksum::fromContent('title', 'body')->value(),
        );

        $checksum = PageChecksum::fromContent('title', 'body');
        self::assertSame($checksum->value(), (string) $checksum);
        self::assertSame($checksum->value(), (new PageChecksum(' '.strtoupper($checksum->value()).' '))->value());
    }

    public function testChecksumRejectsMalformedDigest(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new PageChecksum('not-a-sha256');
    }

    public function testRevisionNumberStartsAtOne(): void
    {
        self::assertSame(1, (new PageRevisionNumber(1))->value());
    }

    public function testRevisionNumberRejectsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new PageRevisionNumber(0);
    }
}
