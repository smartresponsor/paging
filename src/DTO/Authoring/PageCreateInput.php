<?php

declare(strict_types=1);

namespace App\Paging\DTO\Authoring;

use App\Paging\Enum\PageKind;

final readonly class PageCreateInput
{
    public function __construct(
        public string $code,
        public string $slug,
        public string $title,
        public PageKind $kind = PageKind::Page,
        public ?string $ownerUserId = null,
    ) {
    }
}
