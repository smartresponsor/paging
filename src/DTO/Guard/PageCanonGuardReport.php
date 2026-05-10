<?php

declare(strict_types=1);

namespace App\Paging\DTO\Guard;

/**
 * Machine-readable report for the final Paging naming and boundary canon guard.
 */
final readonly class PageCanonGuardReport
{
    /** @param list<PageCanonGuardItem> $items */
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
        return count(array_filter($this->items, static fn (PageCanonGuardItem $item): bool => $item->passed));
    }

    public function failedCount(): int
    {
        return count($this->items) - $this->passedCount();
    }

    /** @return array{passed: bool, passedCount: int, failedCount: int, items: list<array{code: string, label: string, passed: bool, detail: string}>} */
    public function toArray(): array
    {
        return [
            'passed' => $this->passed(),
            'passedCount' => $this->passedCount(),
            'failedCount' => $this->failedCount(),
            'items' => array_map(static fn (PageCanonGuardItem $item): array => $item->toArray(), $this->items),
        ];
    }
}
