<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Operations;

use App\Paging\DTO\Operations\PageOperationChecklistReportDTO;

interface PageOperationChecklistServiceInterface
{
    public function buildReport(): PageOperationChecklistReportDTO;
}
