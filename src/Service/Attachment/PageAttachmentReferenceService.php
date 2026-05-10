<?php

declare(strict_types=1);

namespace App\Paging\Service\Attachment;

use App\Paging\DTO\Attachment\PageAttachmentReferenceInput;
use App\Paging\Entity\PageAttachmentReference;
use App\Paging\ServiceInterface\Attachment\PageAttachmentReferenceServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PageAttachmentReferenceService implements PageAttachmentReferenceServiceInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function attach(PageAttachmentReferenceInput $input): PageAttachmentReference
    {
        $reference = new PageAttachmentReference(
            $input->page,
            $input->attachmentId,
            $input->usage,
            $input->revision,
            $input->attachmentCode,
            $input->position,
        );

        $this->entityManager->persist($reference);
        $this->entityManager->flush();

        return $reference;
    }
}
