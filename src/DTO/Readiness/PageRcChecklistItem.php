<?php

declare(strict_types=1);

namespace App\Paging\DTO\Readiness;

final readonly class PageRcChecklistItem
{
    public function __construct(
        public string $code,
        public string $label,
        public bool $passed,
        public string $detail,
    ) {
    }
}
