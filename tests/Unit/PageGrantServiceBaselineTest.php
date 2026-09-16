<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Security\PageGrantCheck;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageGrant;
use App\Paging\Enum\PageGrantType;
use App\Paging\Service\Security\PageGrantService;
use App\Paging\ValueObject\PageSlug;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class PageGrantServiceBaselineTest extends TestCase
{
    public function testOwnerCanEditOwnPage(): void
    {
        $service = new PageGrantService($this->createStub(EntityManagerInterface::class));
        $page = new Page('about', PageSlug::fromSource('about')->value(), 'About', ownerUserId: 'user-1');

        self::assertTrue($service->isGranted(new PageGrantCheck($page, PageGrantType::Edit, 'user-1')));
        self::assertFalse($service->isGranted(new PageGrantCheck($page, PageGrantType::Publish, 'user-1')));
    }

    public function testAdminCanManageAnyPage(): void
    {
        $service = new PageGrantService($this->createStub(EntityManagerInterface::class));
        $page = new Page('about', PageSlug::fromSource('about')->value(), 'About');

        self::assertTrue($service->isGranted(new PageGrantCheck($page, PageGrantType::Manage, 'admin', ['ROLE_PAGE_ADMIN'])));
    }

    public function testGrantEntityRetainsSecuritySubjectAndAuditIdentity(): void
    {
        $page = new Page('about', PageSlug::fromSource('about')->value(), 'About');
        $grant = new PageGrant($page, PageGrantType::Edit, 'user-2', 'ROLE_EDITOR', 'admin-1');

        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $grant->getId());
        self::assertSame($page, $grant->getPage());
        self::assertSame('user-2', $grant->getSubjectUserId());
        self::assertSame('ROLE_EDITOR', $grant->getSubjectRole());
        self::assertSame(PageGrantType::Edit, $grant->getGrant());
        self::assertSame('admin-1', $grant->getCreatedByUserId());
        self::assertSame('admin-1', $grant->getCreatedBy());
    }
}
