<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;

/**
 * Contract for PageAttachmentReference persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<PageAttachmentReference>
 */
interface PageAttachmentReferenceRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
    public function save(PageAttachmentReference $reference, bool $flush = true): void;
}
