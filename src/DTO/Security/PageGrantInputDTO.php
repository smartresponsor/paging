<?php

declare(strict_types=1);

namespace App\Paging\DTO\Security;

use App\Paging\Entity\PageEntity as Page;
use App\Paging\Enum\PageGrantType;

final readonly class PageGrantInputDTO
{
    public function __construct(
        public Page $page,
        public PageGrantType $grant,
        public ?string $subjectUserId = null,
        public ?string $subjectRole = null,
        public ?string $createdByUserId = null,
    ) {
    }
}
