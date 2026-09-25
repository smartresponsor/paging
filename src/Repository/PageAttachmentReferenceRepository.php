<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;
use App\Paging\RepositoryInterface\PageAttachmentReferenceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine repository for PageAttachmentReference entities.
 *
 * @extends ServiceEntityRepository<PageAttachmentReference>
 */
final class PageAttachmentReferenceRepository extends ServiceEntityRepository implements PageAttachmentReferenceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageAttachmentReference::class);
    }

    public function save(PageAttachmentReference $reference, bool $flush = true): void
    {
        $this->getEntityManager()->persist($reference);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
