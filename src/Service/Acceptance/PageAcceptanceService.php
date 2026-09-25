<?php

declare(strict_types=1);

namespace App\Paging\Service\Acceptance;

use App\Paging\DTO\Acceptance\PageAcceptanceInputDTO;
use App\Paging\DTO\Acceptance\PageAcceptanceViewDTO;
use App\Paging\Entity\PageAcceptanceEntity as PageAcceptance;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\RepositoryInterface\PageAcceptanceRepositoryInterface;
use App\Paging\ServiceInterface\Acceptance\PageAcceptanceServiceInterface;

final readonly class PageAcceptanceService implements PageAcceptanceServiceInterface
{
    public function __construct(
        private PageAcceptanceRepositoryInterface $pageAcceptanceRepository,
    ) {
    }

    public function accept(PageAcceptanceInputDTO $input): PageAcceptance
    {
        $acceptance = new PageAcceptance(
            $input->revision->getPage(),
            $input->revision,
            $input->subjectUserId,
            $this->hashNullable($input->ipAddress),
            $this->hashNullable($input->userAgent),
            $input->acceptanceContext,
        );

        $this->pageAcceptanceRepository->save($acceptance);

        return $acceptance;
    }

    public function hasAccepted(PageRevision $revision, string $subjectUserId): bool
    {
        return null !== $this->pageAcceptanceRepository->findOneBy([
            'revision' => $revision,
            'subjectUserId' => $subjectUserId,
            'revisionChecksum' => $revision->getChecksum(),
        ]);
    }

    public function view(PageAcceptance $acceptance): PageAcceptanceViewDTO
    {
        return new PageAcceptanceViewDTO(
            $acceptance->getId(),
            $acceptance->getPage()->getCode(),
            $acceptance->getRevision()->getRevisionNumber(),
            $acceptance->getSubjectUserId(),
            $acceptance->getRevisionChecksum(),
            $acceptance->getAcceptedAt(),
            $acceptance->getIpHash(),
            $acceptance->getUserAgentHash(),
            $acceptance->getAcceptanceContext(),
        );
    }

    private function hashNullable(?string $value): ?string
    {
        $normalized = trim((string) $value);
        if ('' === $normalized) {
            return null;
        }

        return hash('sha256', $normalized);
    }
}
