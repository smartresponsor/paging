<?php

declare(strict_types=1);

namespace App\Paging\DTO\Bridge;

final readonly class PageBridgeLegalNotice
{
    public function __construct(
        public string $versionLabel,
        public ?\DateTimeImmutable $effectiveFrom = null,
        public ?\DateTimeImmutable $lastUpdatedAt = null,
        public bool $requiresAcceptance = false,
        public ?int $acceptanceRevisionNumber = null,
    ) {
    }

    /** @return array<string, bool|int|string|null> */
    public function toArray(): array
    {
        return [
            'versionLabel' => $this->versionLabel,
            'effectiveFrom' => $this->effectiveFrom?->format(DATE_ATOM),
            'lastUpdatedAt' => $this->lastUpdatedAt?->format(DATE_ATOM),
            'requiresAcceptance' => $this->requiresAcceptance,
            'acceptanceRevisionNumber' => $this->acceptanceRevisionNumber,
        ];
    }
}
