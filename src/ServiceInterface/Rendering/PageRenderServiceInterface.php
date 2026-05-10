<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Rendering;

use App\Paging\DTO\Rendering\PageRenderView;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageRevision;

interface PageRenderServiceInterface
{
    public function renderPublished(Page $page): PageRenderView;

    public function renderRevision(PageRevision $revision): PageRenderView;
}
