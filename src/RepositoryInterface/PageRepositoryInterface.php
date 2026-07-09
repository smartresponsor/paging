<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\Page;

/**
 * Contract for Page persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<Page>
 */
interface PageRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
}
