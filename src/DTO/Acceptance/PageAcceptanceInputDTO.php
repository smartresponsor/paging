<?php

declare(strict_types=1);

namespace App\Paging\DTO\Acceptance;

use App\Paging\Entity\PageRevisionEntity as PageRevision;

final readonly class PageAcceptanceInputDTO
{
    /** @param array<string, mixed>|null $acceptanceContext */
    public function __construct(
        public PageRevision $revision,
        public string $subjectUserId,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?array $acceptanceContext = null,
    ) {
    }
}
