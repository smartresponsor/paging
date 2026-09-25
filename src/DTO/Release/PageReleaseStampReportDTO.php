<?php

declare(strict_types=1);

namespace App\Paging\DTO\Release;

/**
 * Final release stamp report for transferring Paging/Page RC to host integration work.
 */
final readonly class PageReleaseStampReportDTO
{
    /** @param list<PageReleaseStampItemDTO> $items */
    public function __construct(public string $releaseName, public array $items)
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
        return count(array_filter($this->items, static fn (PageReleaseStampItemDTO $item): bool => $item->passed));
    }

    public function failedCount(): int
    {
        return count($this->items) - $this->passedCount();
    }

    /** @return array{component: string, namespace: string, businessPrefix: string, configRoot: string, tablePrefix: string, releaseName: string, passed: bool, passedCount: int, failedCount: int, items: list<array{code: string, label: string, passed: bool, detail: string}>} */
    public function toArray(): array
    {
        return [
            'component' => 'Paging',
            'namespace' => 'App\\Paging',
            'businessPrefix' => 'Page',
            'configRoot' => 'page',
            'tablePrefix' => 'page_',
            'releaseName' => $this->releaseName,
            'passed' => $this->passed(),
            'passedCount' => $this->passedCount(),
            'failedCount' => $this->failedCount(),
            'items' => array_map(static fn (PageReleaseStampItemDTO $item): array => $item->toArray(), $this->items),
        ];
    }
}
