<?php

declare(strict_types=1);

namespace App\Paging\DTO\Operations;

/**
 * One operational readiness item for the Paging component handoff.
 */
final readonly class PageOperationChecklistItem
{
    public function __construct(
        public string $code,
        public string $label,
        public bool $passed,
        public string $detail,
    ) {
    }
}
