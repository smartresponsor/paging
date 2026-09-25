<?php

declare(strict_types=1);

namespace App\Paging\DTO\Usability;

/**
 * Deterministic report describing what is ready for full user-facing host use.
 */
final readonly class PageUserUsabilityReportDTO
{
    /** @param list<PageUserUsabilityItemDTO> $items */
    public function __construct(public array $items)
    {
    }

    public function componentReady(): bool
    {
        foreach ($this->items as $item) {
            if (!$item->componentReady) {
                return false;
            }
        }

        return true;
    }

    public function readyCount(): int
    {
        return count(array_filter($this->items, static fn (PageUserUsabilityItemDTO $item): bool => $item->componentReady));
    }

    public function pendingCount(): int
    {
        return count($this->items) - $this->readyCount();
    }

    /** @return list<string> */
    public function ownerLayers(): array
    {
        return array_values(array_unique(array_map(
            static fn (PageUserUsabilityItemDTO $item): string => $item->ownerLayer,
            $this->items,
        )));
    }

    /** @return array{componentReady: bool, readyCount: int, pendingCount: int, ownerLayers: list<string>, items: list<array{code: string, ownerLayer: string, componentReady: bool, detail: string}>} */
    public function toArray(): array
    {
        return [
            'componentReady' => $this->componentReady(),
            'readyCount' => $this->readyCount(),
            'pendingCount' => $this->pendingCount(),
            'ownerLayers' => $this->ownerLayers(),
            'items' => array_map(static fn (PageUserUsabilityItemDTO $item): array => [
                'code' => $item->code,
                'ownerLayer' => $item->ownerLayer,
                'componentReady' => $item->componentReady,
                'detail' => $item->detail,
            ], $this->items),
        ];
    }
}
