<?php

declare(strict_types=1);

namespace App\Paging\DTO\Revision;

final readonly class PageRevisionCreateInput
{
    /**
     * @param array<string, mixed>|null $bodyJson
     */
    public function __construct(
        public string $title,
        public string $bodyHtml,
        public ?string $bodyText = null,
        public ?string $bodyMarkdown = null,
        public ?array $bodyJson = null,
        public ?string $changeNote = null,
        public ?string $createdByUserId = null,
    ) {
    }
}
