<?php

declare(strict_types=1);

namespace App\Paging\DTO\Export;

use App\Paging\Enum\PageExportFormat;

final readonly class PageExportView
{
    public function __construct(
        public string $code,
        public string $slug,
        public string $title,
        public PageExportFormat $format,
        public string $content,
        public string $contentType,
        public string $checksum,
    ) {
    }
}
