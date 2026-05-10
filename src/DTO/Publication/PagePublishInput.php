<?php

declare(strict_types=1);

namespace App\Paging\DTO\Publication;

final readonly class PagePublishInput
{
    public function __construct(
        public ?\DateTimeImmutable $effectiveFrom = null,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?string $publishedByUserId = null,
    ) {
    }
}
