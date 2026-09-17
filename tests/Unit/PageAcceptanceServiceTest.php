<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Acceptance\PageAcceptanceInput;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageAcceptance;
use App\Paging\Entity\PageRevision;
use App\Paging\Enum\PageKind;
use App\Paging\ValueObject\PageSlug;
use PHPUnit\Framework\TestCase;

final class PageAcceptanceServiceTest extends TestCase
{
    public function testAcceptanceViewUsesRevisionChecksum(): void
    {
        $page = new Page('privacy_policy', PageSlug::fromSource('privacy_policy')->value(), 'Privacy Policy', PageKind::Policy, 'owner-1');
        $revision = new PageRevision($page, 1, 'Privacy Policy', '<p>Policy</p>', 'Policy');
        $acceptance = new PageAcceptance($page, $revision, 'user-1', 'ip-hash', 'agent-hash', ['surface' => 'test']);

        self::assertSame($page, $acceptance->getPage());
        self::assertSame($revision, $acceptance->getRevision());
        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $acceptance->getId());
        self::assertSame($revision->getChecksum(), $acceptance->getRevisionChecksum());
        self::assertSame('user-1', $acceptance->getSubjectUserId());
        self::assertSame('ip-hash', $acceptance->getIpHash());
        self::assertSame('agent-hash', $acceptance->getUserAgentHash());
        self::assertInstanceOf(\DateTimeImmutable::class, $acceptance->getAcceptedAt());
        self::assertSame(['surface' => 'test'], $acceptance->getAcceptanceContext());
    }

    public function testAcceptanceInputCarriesRevisionAndSubject(): void
    {
        $page = new Page('terms', PageSlug::fromSource('terms')->value(), 'Terms', PageKind::Policy, null);
        $revision = new PageRevision($page, 1, 'Terms', '<p>Terms</p>', 'Terms');
        $input = new PageAcceptanceInput($revision, 'user-2', '127.0.0.1', 'UnitTest', ['surface' => 'signup']);

        self::assertSame($revision, $input->revision);
        self::assertSame('user-2', $input->subjectUserId);
        self::assertSame(['surface' => 'signup'], $input->acceptanceContext);
    }
}
