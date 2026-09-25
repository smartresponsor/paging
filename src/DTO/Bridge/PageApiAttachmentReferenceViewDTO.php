<?php

declare(strict_types=1);

namespace App\Paging\DTO\Bridge;

use App\Paging\Enum\PageAttachmentUsage;

final readonly class PageApiAttachmentReferenceViewDTO
{
    public function __construct(
        public string $attachmentId,
        public PageAttachmentUsage $usage,
        public ?string $attachmentCode = null,
        public int $position = 0,
    ) {
    }
}
