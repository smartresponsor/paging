<?php

declare(strict_types=1);

namespace App\Paging\DTO\Finalization;

/**
 * One machine-readable final RC status check item.
 */
final readonly class PageFinalStatusItem
{
    public function __construct(
        public string $code,
        public string $label,
        public bool $passed,
        public string $detail,
    ) {
    }
}
