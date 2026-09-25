<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Attachment;

use App\Paging\DTO\Attachment\PageAttachmentReferenceInputDTO;
use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;

interface PageAttachmentReferenceServiceInterface
{
    public function attach(PageAttachmentReferenceInputDTO $input): PageAttachmentReference;
}
