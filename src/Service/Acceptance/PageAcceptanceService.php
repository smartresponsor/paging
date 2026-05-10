<?php

declare(strict_types=1);

namespace App\Paging\Service\Acceptance;

use App\Paging\DTO\Acceptance\PageAcceptanceInput;
use App\Paging\DTO\Acceptance\PageAcceptanceView;
use App\Paging\Entity\PageAcceptance;
use App\Paging\Entity\PageRevision;
use App\Paging\Repository\PageAcceptanceRepository;
use App\Paging\ServiceInterface\Acceptance\PageAcceptanceServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PageAcceptanceService implements PageAcceptanceServiceInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PageAcceptanceRepository $pageAcceptanceRepository,
    ) {
    }

    public function accept(PageAcceptanceInput $input): PageAcceptance
    {
        $acceptance = new PageAcceptance(
            $input->revision->getPage(),
            $input->revision,
            $input->subjectUserId,
            $this->hashNullable($input->ipAddress),
            $this->hashNullable($input->userAgent),
            $input->acceptanceContext,
        );

        $this->entityManager->persist($acceptance);
        $this->entityManager->flush();

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

    public function view(PageAcceptance $acceptance): PageAcceptanceView
    {
        return new PageAcceptanceView(
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
