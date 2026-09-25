<?php

declare(strict_types=1);

namespace App\Paging\RepositoryInterface;

use App\Paging\Entity\PageEntity as Page;

/**
 * Contract for Page persistence access.
 *
 * @extends \Doctrine\Persistence\ObjectRepository<Page>
 */
interface PageRepositoryInterface extends \Doctrine\Persistence\ObjectRepository
{
    public function save(Page $page, bool $flush = true): void;

    public function flush(): void;

    /** @param class-string $entityClass */
    public function tableNameFor(string $entityClass): string;
}
