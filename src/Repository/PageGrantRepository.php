<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\PageGrant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine repository for PageGrant entities.
 *
 * @extends ServiceEntityRepository<PageGrant>
 */
final class PageGrantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageGrant::class);
    }
}
