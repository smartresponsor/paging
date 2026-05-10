<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Controller\Public\PageViewController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Attribute\Route;

final class PagePublicRoutesTest extends TestCase
{
    public function testPublicRoutesAreCanonicalized(): void
    {
        $indexRoutes = (new \ReflectionMethod(PageViewController::class, 'index'))->getAttributes(Route::class);
        $viewRoutes = (new \ReflectionMethod(PageViewController::class, '__invoke'))->getAttributes(Route::class);

        self::assertCount(1, $indexRoutes);
        self::assertCount(1, $viewRoutes);
        self::assertSame('/page/', $indexRoutes[0]->getArguments()[0]);
        self::assertSame('/page/{slug}', $viewRoutes[0]->getArguments()[0]);
    }
}
