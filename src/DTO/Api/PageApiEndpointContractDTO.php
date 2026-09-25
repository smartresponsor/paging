<?php

declare(strict_types=1);

namespace App\Paging\DTO\Api;

/**
 * Describes one stable public or API endpoint exposed by the Paging component.
 *
 * The DTO is intentionally framework-light: Backofficing, Interfacing, API docs,
 * and smoke tools can read the same contract without coupling to route objects.
 */
final readonly class PageApiEndpointContractDTO
{
    /** @param list<string> $formats */
    public function __construct(
        public string $nameEntity,
        public string $method,
        public string $path,
        public string $purpose,
        public array $formats = [],
        public bool $requiresPublishedRevision = false,
        public bool $writesState = false,
    ) {
    }
}
