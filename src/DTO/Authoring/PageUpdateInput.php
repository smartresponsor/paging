<?php

declare(strict_types=1);

namespace App\Paging\DTO\Authoring;

final readonly class PageUpdateInput
{
    public function __construct(
        public string $title,
        public string $slug,
        public ?string $ownerUserId = null,
    ) {
    }
}
