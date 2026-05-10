<?php

declare(strict_types=1);

namespace App\Paging\DTO\Finalization;

/**
 * Final RC status report for Paging/Page.
 */
final readonly class PageFinalStatusReport
{
    /** @param list<PageFinalStatusItem> $items */
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
        return count(array_filter($this->items, static fn (PageFinalStatusItem $item): bool => $item->passed));
    }

    public function failedCount(): int
    {
        return count($this->items) - $this->passedCount();
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'component' => 'Paging',
            'business_prefix' => 'Page',
            'database_prefix' => 'page_',
            'config_prefix' => 'page',
            'passed' => $this->passed(),
            'passedCount' => $this->passedCount(),
            'failedCount' => $this->failedCount(),
            'items' => array_map(
                static fn (PageFinalStatusItem $item): array => [
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
