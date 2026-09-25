<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Revision;

use App\Paging\DTO\Revision\PageRevisionCreateInputDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;

interface PageRevisionServiceInterface
{
    public function createRevision(Page $page, PageRevisionCreateInputDTO $input): PageRevision;
}
