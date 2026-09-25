<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Handoff;

use App\Paging\DTO\Handoff\PageHandoffReportDTO;

interface PageHandoffSummaryServiceInterface
{
    public function buildReport(): PageHandoffReportDTO;
}
