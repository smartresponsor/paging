<?php

declare(strict_types=1);

namespace App\Paging\DTO\Attachment;

use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Enum\PageAttachmentUsage;

final readonly class PageAttachmentReferenceInputDTO
{
    public function __construct(
        public Page $page,
        public string $attachmentId,
        public PageAttachmentUsage $usage = PageAttachmentUsage::Inline,
        public ?PageRevision $revision = null,
        public ?string $attachmentCode = null,
        public int $position = 0,
    ) {
    }
}
