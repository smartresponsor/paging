<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Authoring;

use App\Paging\DTO\Authoring\PageCreateInput;
use App\Paging\DTO\Authoring\PageUpdateInput;
use App\Paging\Entity\Page;

interface PageDraftServiceInterface
{
    public function createPage(PageCreateInput $input): Page;

    public function updatePage(Page $page, PageUpdateInput $input): Page;
}
