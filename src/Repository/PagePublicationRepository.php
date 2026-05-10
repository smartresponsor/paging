<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\PagePublication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine repository for PagePublication entities.
 *
 * @extends ServiceEntityRepository<PagePublication>
 */
final class PagePublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PagePublication::class);
    }
}
