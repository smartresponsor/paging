<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\PageAcceptanceEntity as PageAcceptance;

/**
 * Contract for PageAcceptance persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<PageAcceptance>
 */
interface PageAcceptanceRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
    public function save(PageAcceptance $acceptance, bool $flush = true): void;
}
