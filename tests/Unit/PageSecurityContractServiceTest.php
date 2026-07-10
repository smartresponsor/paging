<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Service\Security\PageSecurityContractService;
use App\Paging\Voter\PageVoter;
use PHPUnit\Framework\TestCase;

final class PageSecurityContractServiceTest extends TestCase
{
    public function testBuildReportDeclaresHostSecurityContract(): void
    {
        $report = (new PageSecurityContractService())->buildReport();
        $payload = $report->toArray();
        self::assertTrue($report->passed());
        self::assertSame('paging', $payload['component']);
        self::assertSame('host-security', $payload['consumer']);
        self::assertContains(PageVoter::VIEW, $payload['voterAttributes']);
        self::assertContains(PageVoter::PUBLISH, $payload['voterAttributes']);
        self::assertContains('view', $payload['grantTypes']);
        self::assertContains('manage', $payload['grantTypes']);
        self::assertContains('ROLE_ADMIN', $payload['globalRoles']);
        self::assertContains('role_hierarchy', $payload['hostOwnedSurfaces']);
    }
}
