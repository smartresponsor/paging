<?php

declare(strict_types=1);

namespace App\Paging\DTO\Operations;

/**
 * Aggregates final post-RC operational checks for standalone and host usage.
 */
final readonly class PageOperationChecklistReport
{
    /** @param list<PageOperationChecklistItem> $items */
    public function __construct(public array $items)
    {
    }

    public function passed(): bool
    {
        foreach ($this->items as $item) {
            if (!$item->passed) {
                return false;
            }
        }

        return true;
    }

    public function passedCount(): int
    {
        return count(array_filter($this->items, static fn (PageOperationChecklistItem $item): bool => $item->passed));
    }

    public function failedCount(): int
    {
        return count($this->items) - $this->passedCount();
    }
}
