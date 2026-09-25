<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Export;

use App\Paging\DTO\Export\PageExportViewDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Enum\PageExportFormat;

interface PageExportServiceInterface
{
    public function exportPublished(Page $page, PageExportFormat $format): PageExportViewDTO;
}
