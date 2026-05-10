<?php

declare(strict_types=1);

namespace App\Paging\DTO\Security;

use App\Paging\Entity\Page;
use App\Paging\Enum\PageGrantType;

final readonly class PageGrantCheck
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        public Page $page,
        public PageGrantType $grant,
        public ?string $userId = null,
        public array $roles = [],
    ) {
    }
}
