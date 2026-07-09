<?php

declare(strict_types=1);

namespace App\Paging\Repository;

use App\Paging\Entity\Page;
use App\Paging\RepositoryInterface\PageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine repository for Page entities.
 *
 * @extends ServiceEntityRepository<Page>
 */
final class PageRepository extends ServiceEntityRepository implements PageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @return list<Page>
     */
    public function findPublishedOrdered(): array
    {
        return $this->createQueryBuilder('page')
            ->andWhere('page.publishedRevision IS NOT NULL')
            ->orderBy('page.updatedAt', 'DESC')
            ->addOrderBy('page.slug', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
