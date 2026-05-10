<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Security\PageGrantCheck;
use App\Paging\Entity\Page;
use App\Paging\Enum\PageGrantType;
use App\Paging\Service\Security\PageGrantService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class PageGrantServiceBaselineTest extends TestCase
{
    public function testOwnerCanEditOwnPage(): void
    {
        $service = new PageGrantService($this->createMock(EntityManagerInterface::class));
        $page = new Page('about', 'about', 'About', ownerUserId: 'user-1');

        self::assertTrue($service->isGranted(new PageGrantCheck($page, PageGrantType::Edit, 'user-1')));
        self::assertFalse($service->isGranted(new PageGrantCheck($page, PageGrantType::Publish, 'user-1')));
    }

    public function testAdminCanManageAnyPage(): void
    {
        $service = new PageGrantService($this->createMock(EntityManagerInterface::class));
        $page = new Page('about', 'about', 'About');

        self::assertTrue($service->isGranted(new PageGrantCheck($page, PageGrantType::Manage, 'admin', ['ROLE_PAGE_ADMIN'])));
    }
}
