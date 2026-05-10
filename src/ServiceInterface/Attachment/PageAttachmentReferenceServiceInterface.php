<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Attachment;

use App\Paging\DTO\Attachment\PageAttachmentReferenceInput;
use App\Paging\Entity\PageAttachmentReference;

interface PageAttachmentReferenceServiceInterface
{
    public function attach(PageAttachmentReferenceInput $input): PageAttachmentReference;
}
