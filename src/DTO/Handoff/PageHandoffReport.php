<?php

declare(strict_types=1);

namespace App\Paging\DTO\Handoff;

/**
 * Machine-readable handoff summary for the post-RC Paging component.
 */
final readonly class PageHandoffReport
{
    /** @param list<PageHandoffItem> $items */
    public function __construct(public array $items)
    {
    }

    public function status(): string
    {
        foreach ($this->items as $item) {
            if ('ready' !== $item->status) {
                return 'attention_required';
            }
        }

        return 'ready';
    }

    public function passed(): bool
    {
        return 'ready' === $this->status();
    }

    public function passedCount(): int
    {
        return count(array_filter($this->items, static fn (PageHandoffItem $item): bool => 'ready' === $item->status));
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
            'namespace' => 'App\\Paging',
            'business_stem' => 'Page',
            'database_prefix' => 'page_',
            'config_prefix' => 'page',
            'status' => $this->status(),
            'items' => array_map(
                static fn (PageHandoffItem $item): array => $item->toArray(),
                $this->items,
            ),
            'next_owner_layers' => [
                'Backofficing for EasyAdmin/operator CRUD surfaces',
                'Interfacing for public/admin visual shell rendering',
                'Attachment for file/media storage and retrieval',
                'Locale for locale ownership and translation policy',
            ],
        ];
    }
}
