<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\PageAcceptance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PageAcceptance> */
final class PageAcceptanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageAcceptance::class);
    }
}
