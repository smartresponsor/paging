<?php

declare(strict_types=1);

namespace App\Paging\DTO\Editor;

final readonly class PageEditorPayloadDTO
{
    /**
     * @param array<string, mixed>|null $bodyJson
     */
    public function __construct(
        public string $title,
        public string $bodyHtml,
        public string $bodyText,
        public ?string $bodyMarkdown = null,
        public ?array $bodyJson = null,
    ) {
    }
}
