<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Finalization;

use App\Paging\DTO\Finalization\PageFinalStatusReportDTO;

interface PageFinalStatusServiceInterface
{
    public function buildReport(): PageFinalStatusReportDTO;
}
