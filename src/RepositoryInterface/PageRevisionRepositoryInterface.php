<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\PageRevisionEntity as PageRevision;

/**
 * Contract for PageRevision persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<PageRevision>
 */
interface PageRevisionRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
    public function save(PageRevision $revision, bool $flush = true): void;
}
