<?php

declare(strict_types=1);

namespace App\Paging\Service\Attachment;

use App\Paging\DTO\Attachment\PageAttachmentReferenceInputDTO;
use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;
use App\Paging\RepositoryInterface\PageAttachmentReferenceRepositoryInterface;
use App\Paging\ServiceInterface\Attachment\PageAttachmentReferenceServiceInterface;

final readonly class PageAttachmentReferenceService implements PageAttachmentReferenceServiceInterface
{
    public function __construct(private PageAttachmentReferenceRepositoryInterface $pageAttachmentReferenceRepository)
    {
    }

    public function attach(PageAttachmentReferenceInputDTO $input): PageAttachmentReference
    {
        $reference = new PageAttachmentReference(
            $input->page,
            $input->attachmentId,
            $input->usage,
            $input->revision,
            $input->attachmentCode,
            $input->position,
        );

        $this->pageAttachmentReferenceRepository->save($reference);

        return $reference;
    }
}
