<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Editor\PageContentSanitizer;
use PHPUnit\Framework\TestCase;

final class PageContentSanitizerTest extends TestCase
{
    public function testItRemovesExecutableHtmlButPreservesBasicContent(): void
    {
        $sanitizer = new PageContentSanitizer();

        $html = $sanitizer->sanitizeHtml('<h1 onclick="bad()">Title</h1><script>alert(1)</script><p><a href="javascript:bad()">link</a></p>');

        self::assertStringContainsString('<h1>Title</h1>', $html);
        self::assertStringNotContainsString('script', $html);
        self::assertStringNotContainsString('onclick', $html);
        self::assertStringNotContainsString('javascript:', $html);
    }

    public function testItBuildsPlainText(): void
    {
        $sanitizer = new PageContentSanitizer();

        self::assertSame('Title Body', $sanitizer->toPlainText('<h1>Title</h1><p>Body</p>'));
    }
}
