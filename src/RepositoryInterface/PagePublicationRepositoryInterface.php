<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\PagePublicationEntity as PagePublication;

/**
 * Contract for PagePublication persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<PagePublication>
 */
interface PagePublicationRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
    public function save(PagePublication $publication, bool $flush = true): void;
}
