<?php

declare(strict_types=1);

namespace App\Paging\Service\Security;

use App\Paging\DTO\Security\PageSecurityContractReport;
use App\Paging\Entity\PageGrant;
use App\Paging\Enum\PageGrantType;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;
use App\Paging\ServiceInterface\Security\PageSecurityContractServiceInterface;
use App\Paging\ServiceInterface\Security\PageSecuritySubjectResolverInterface;
use App\Paging\Voter\PageVoter;

final class PageSecurityContractService implements PageSecurityContractServiceInterface
{
    public function buildReport(): PageSecurityContractReport
    {
        return new PageSecurityContractReport(
            PageGrant::class, PageVoter::class, PageSecuritySubjectResolverInterface::class, PageGrantServiceInterface::class,
            [PageVoter::VIEW, PageVoter::EDIT, PageVoter::PUBLISH, PageVoter::MANAGE],
            array_map(static fn (PageGrantType $grant): string => $grant->value, PageGrantType::cases()),
            ['ROLE_ADMIN', 'ROLE_PAGE_ADMIN'],
            ['authentication', 'role_hierarchy', 'firewalls', 'user_provider', 'access_control']
        );
    }
}
