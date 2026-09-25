<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\PageGrantEntity as PageGrant;

/**
 * Contract for PageGrant persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<PageGrant>
 */
interface PageGrantRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
    public function save(PageGrant $grant, bool $flush = true): void;
}
