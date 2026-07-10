<?php

declare(strict_types=1);

namespace App\Paging\DTO\Usability;

/**
 * One host-facing user usability requirement for Paging.
 */
final readonly class PageUserUsabilityItem
{
    public function __construct(
        public string $code,
        public string $ownerLayer,
        public bool $componentReady,
        public string $detail,
    ) {
    }
}
