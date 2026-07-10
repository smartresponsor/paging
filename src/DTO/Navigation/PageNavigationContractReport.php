<?php

declare(strict_types=1);

namespace App\Paging\DTO\Navigation;

final readonly class PageNavigationContractReport
{
    /** @param list<PageNavigationItem> $items */
    public function __construct(public array $items)
    {
    }

    public function itemCount(): int
    {
        return count($this->items);
    }

    /** @return list<string> */
    public function routeNames(): array
    {
        return array_map(static fn (PageNavigationItem $item): string => $item->routeName, $this->items);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'component' => 'paging',
            'location' => 'shell.left.bottom',
            'itemCount' => $this->itemCount(),
            'routeNames' => $this->routeNames(),
            'items' => array_map(static fn (PageNavigationItem $item): array => $item->toArray(), $this->items),
            'navigatingEntitySeeds' => array_map(static fn (PageNavigationItem $item): array => $item->toNavigatingEntitySeed(), $this->items),
        ];
    }
}
