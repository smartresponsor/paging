<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\PageRevision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine repository for PageRevision entities.
 *
 * @extends ServiceEntityRepository<PageRevision>
 */
final class PageRevisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageRevision::class);
    }
}
