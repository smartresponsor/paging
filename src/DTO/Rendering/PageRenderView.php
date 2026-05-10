<?php

declare(strict_types=1);

namespace App\Paging\DTO\Rendering;

use App\Paging\Enum\PageKind;

final readonly class PageRenderView
{
    public function __construct(
        public string $code,
        public string $slug,
        public string $title,
        public PageKind $kind,
        public int $version,
        public string $bodyHtml,
        public string $bodyText,
        public ?string $bodyMarkdown,
        public string $checksum,
        public ?\DateTimeImmutable $publishedAt,
        public ?\DateTimeImmutable $effectiveFrom,
    ) {
    }
}
