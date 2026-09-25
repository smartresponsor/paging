<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\RepositoryInterface\PageRevisionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine repository for PageRevision entities.
 *
 * @extends ServiceEntityRepository<PageRevision>
 */
final class PageRevisionRepository extends ServiceEntityRepository implements PageRevisionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageRevision::class);
    }

    public function save(PageRevision $revision, bool $flush = true): void
    {
        $this->getEntityManager()->persist($revision);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
