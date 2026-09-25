<?php

declare(strict_types=1);

namespace App\Paging\DTO\Completion;

/**
 * Final machine-readable completion report for Paging RC closure.
 */
final readonly class PageCompletionReportDTO
{
    /** @param list<PageCompletionItemDTO> $items */
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
        return count(array_filter($this->items, static fn (PageCompletionItemDTO $item): bool => $item->passed));
    }

    public function failedCount(): int
    {
        return count($this->items) - $this->passedCount();
    }

    /** @return array{component: string, businessPrefix: string, passed: bool, passedCount: int, failedCount: int, items: list<array{code: string, label: string, passed: bool, detail: string}>} */
    public function toArray(): array
    {
        return [
            'component' => 'Paging',
            'businessPrefix' => 'Page',
            'passed' => $this->passed(),
            'passedCount' => $this->passedCount(),
            'failedCount' => $this->failedCount(),
            'items' => array_map(static fn (PageCompletionItemDTO $item): array => $item->toArray(), $this->items),
        ];
    }
}
