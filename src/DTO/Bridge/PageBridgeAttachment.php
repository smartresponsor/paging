<?php

declare(strict_types=1);

namespace App\Paging\DTO\Bridge;

use App\Paging\Enum\PageAttachmentUsage;

final readonly class PageBridgeAttachment
{
    public function __construct(
        public string $attachmentId,
        public PageAttachmentUsage $usage,
        public ?string $attachmentCode = null,
        public int $position = 0,
        public ?string $label = null,
        public ?string $altText = null,
    ) {
    }

    /** @return array<string, int|string|null> */
    public function toArray(): array
    {
        return [
            'attachmentId' => $this->attachmentId,
            'attachmentCode' => $this->attachmentCode,
            'usage' => $this->usage->value,
            'position' => $this->position,
            'label' => $this->label,
            'altText' => $this->altText,
        ];
    }
}
