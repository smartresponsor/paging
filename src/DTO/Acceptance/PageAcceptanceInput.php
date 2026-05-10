<?php

declare(strict_types=1);

namespace App\Paging\DTO\Acceptance;

use App\Paging\Entity\PageRevision;

final readonly class PageAcceptanceInput
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
