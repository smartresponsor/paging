<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Rendering;

use App\Paging\DTO\Rendering\PageRenderViewDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;

interface PageRenderServiceInterface
{
    public function renderPublished(Page $page): PageRenderViewDTO;

    public function renderRevision(PageRevision $revision): PageRenderViewDTO;
}
