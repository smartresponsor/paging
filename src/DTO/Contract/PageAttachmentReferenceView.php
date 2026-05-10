<?php

declare(strict_types=1);

namespace App\Paging\DTO\Contract;

use App\Paging\Enum\PageAttachmentUsage;

final readonly class PageAttachmentReferenceView
{
    public function __construct(
        public string $attachmentId,
        public PageAttachmentUsage $usage,
        public ?string $attachmentCode = null,
        public int $position = 0,
    ) {
    }
}
