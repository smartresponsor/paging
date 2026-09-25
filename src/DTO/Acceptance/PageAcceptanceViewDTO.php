<?php

declare(strict_types=1);

namespace App\Paging\DTO\Acceptance;

final readonly class PageAcceptanceViewDTO
{
    /** @param array<string, mixed>|null $acceptanceContext */
    public function __construct(
        public string $id,
        public string $pageCode,
        public int $revisionNumber,
        public string $subjectUserId,
        public string $revisionChecksum,
        public \DateTimeImmutable $acceptedAt,
        public ?string $ipHash,
        public ?string $userAgentHash,
        public ?array $acceptanceContext,
    ) {
    }
}
