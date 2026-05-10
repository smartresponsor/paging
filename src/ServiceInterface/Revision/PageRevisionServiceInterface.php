<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Revision;

use App\Paging\DTO\Revision\PageRevisionCreateInput;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageRevision;

interface PageRevisionServiceInterface
{
    public function createRevision(Page $page, PageRevisionCreateInput $input): PageRevision;
}
