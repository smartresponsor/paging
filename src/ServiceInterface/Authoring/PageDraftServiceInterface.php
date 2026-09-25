<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Authoring;

use App\Paging\DTO\Authoring\PageCreateInputDTO;
use App\Paging\DTO\Authoring\PageUpdateInputDTO;
use App\Paging\Entity\PageEntity as Page;

interface PageDraftServiceInterface
{
    public function createPage(PageCreateInputDTO $input): Page;

    public function updatePage(Page $page, PageUpdateInputDTO $input): Page;
}
