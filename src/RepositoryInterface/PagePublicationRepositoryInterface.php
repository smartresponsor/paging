<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\PagePublication;

/**
 * Contract for PagePublication persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<PagePublication>
 */
interface PagePublicationRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
}
