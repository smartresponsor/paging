<?php

declare(strict_types=1);

namespace App\Paging\DTO\Readiness;

/**
 * Immutable release-candidate readiness report for the Paging component.
 *
 * The report is intentionally small and deterministic so it can be consumed by
 * a standalone command, host-application smoke checks, or future Backofficing
 * dashboards without coupling Paging to any admin UI implementation.
 */
final readonly class PageRcReadinessReport
{
    /** @param list<PageRcChecklistItem> $items */
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
        return count(array_filter($this->items, static fn (PageRcChecklistItem $item): bool => $item->passed));
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
            'items' => array_map(
                static fn (PageRcChecklistItem $item): array => [
                    'code' => $item->code,
                    'label' => $item->label,
                    'passed' => $item->passed,
                    'detail' => $item->detail,
                ],
                $this->items,
            ),
        ];
    }
}
