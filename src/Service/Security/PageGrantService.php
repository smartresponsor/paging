<?php

declare(strict_types=1);

namespace App\Paging\Service\Security;

use App\Paging\DTO\Security\PageGrantCheckDTO;
use App\Paging\DTO\Security\PageGrantInputDTO;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Enum\PageGrantType;
use App\Paging\RepositoryInterface\PageGrantRepositoryInterface;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;

final readonly class PageGrantService implements PageGrantServiceInterface
{
    public function __construct(private PageGrantRepositoryInterface $pageGrantRepository)
    {
    }

    public function grant(PageGrantInputDTO $input): PageGrant
    {
        $grant = new PageGrant($input->page, $input->grant, $input->subjectUserId, $input->subjectRole, $input->createdByUserId);
        $this->pageGrantRepository->save($grant);

        return $grant;
    }

    public function isGranted(PageGrantCheckDTO $check): bool
    {
        if ($this->hasGlobalAuthority($check->roles)) {
            return true;
        }

        if (PageGrantType::View === $check->grant && null !== $check->page->getPublishedRevision()) {
            return true;
        }

        if (null !== $check->userId && $check->page->getOwnerUserId() === $check->userId && $this->ownerCovers($check->grant)) {
            return true;
        }

        foreach ($check->page->getGrants() as $grant) {
            if ($grant->getGrant() !== $check->grant && PageGrantType::Manage !== $grant->getGrant()) {
                continue;
            }

            if (null !== $check->userId && $grant->getSubjectUserId() === $check->userId) {
                return true;
            }

            if (null !== $grant->getSubjectRole() && in_array($grant->getSubjectRole(), $check->roles, true)) {
                return true;
            }
        }

        return false;
    }

    /** @param list<string> $roles */
    private function hasGlobalAuthority(array $roles): bool
    {
        return in_array('ROLE_ADMIN', $roles, true) || in_array('ROLE_PAGE_ADMIN', $roles, true);
    }

    private function ownerCovers(PageGrantType $grant): bool
    {
        return in_array($grant, [PageGrantType::View, PageGrantType::Edit, PageGrantType::Own], true);
    }
}
