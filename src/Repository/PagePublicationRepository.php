<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\RepositoryInterface\PagePublicationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine repository for PagePublication entities.
 *
 * @extends ServiceEntityRepository<PagePublication>
 */
final class PagePublicationRepository extends ServiceEntityRepository implements PagePublicationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PagePublication::class);
    }

    public function save(PagePublication $publication, bool $flush = true): void
    {
        $this->getEntityManager()->persist($publication);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
