<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Export;

use App\Paging\DTO\Export\PageExportView;
use App\Paging\Entity\Page;
use App\Paging\Enum\PageExportFormat;

interface PageExportServiceInterface
{
    public function exportPublished(Page $page, PageExportFormat $format): PageExportView;
}
