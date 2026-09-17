<?php

declare(strict_types=1);

namespace App\Paging\DTO\Api;

/**
 * Stable contract report for the Page API/output surface.
 */
final readonly class PageApiContractReport
{
    /** @param list<PageApiEndpointContract> $endpoints */
    public function __construct(public array $endpoints)
    {
    }

    public function endpointCount(): int
    {
        return count($this->endpoints);
    }

    /** @return list<array{nameEntity: string, method: string, path: string, purpose: string, formats: list<string>, requiresPublishedRevision: bool, writesState: bool}> */
    public function toArray(): array
    {
        return array_map(
            static fn (PageApiEndpointContract $endpoint): array => [
                'nameEntity' => $endpoint->nameEntity,
                'method' => $endpoint->method,
                'path' => $endpoint->path,
                'purpose' => $endpoint->purpose,
                'formats' => $endpoint->formats,
                'requiresPublishedRevision' => $endpoint->requiresPublishedRevision,
                'writesState' => $endpoint->writesState,
            ],
            $this->endpoints,
        );
    }
}
