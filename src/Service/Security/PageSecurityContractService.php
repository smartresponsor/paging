<?php

declare(strict_types=1);

namespace App\Paging\Service\Security;

use App\Paging\DTO\Security\PageSecurityContractReportDTO;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Enum\PageGrantType;
use App\Paging\ResolverInterface\Security\PageSecuritySubjectResolverInterface;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;
use App\Paging\ServiceInterface\Security\PageSecurityContractServiceInterface;
use App\Paging\Voter\PageVoter;

final class PageSecurityContractService implements PageSecurityContractServiceInterface
{
    public function buildReport(): PageSecurityContractReportDTO
    {
        return new PageSecurityContractReportDTO(
            PageGrant::class, PageVoter::class, PageSecuritySubjectResolverInterface::class, PageGrantServiceInterface::class,
            [PageVoter::VIEW, PageVoter::EDIT, PageVoter::PUBLISH, PageVoter::MANAGE],
            array_map(static fn (PageGrantType $grant): string => $grant->value, PageGrantType::cases()),
            ['ROLE_ADMIN', 'ROLE_PAGE_ADMIN'],
            ['authentication', 'role_hierarchy', 'firewalls', 'user_provider', 'access_control']
        );
    }
}
