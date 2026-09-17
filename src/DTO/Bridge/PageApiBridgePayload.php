<?php

declare(strict_types=1);

namespace App\Paging\DTO\Bridge;

use App\Paging\Enum\PageKind;

final readonly class PageApiBridgePayload
{
    /**
     * @param list<PageApiAttachmentReferenceView> $attachments
     * @param array<string, mixed>                 $renderHints
     */
    public function __construct(
        public string $code,
        public string $slug,
        public string $title,
        public PageKind $kind,
        public int $version,
        public string $bodyHtml,
        public string $bodyText,
        public string $checksum,
        public array $attachments = [],
        public ?\DateTimeImmutable $publishedAt = null,
        public ?\DateTimeImmutable $effectiveFrom = null,
        public array $renderHints = [],
    ) {
    }
}
