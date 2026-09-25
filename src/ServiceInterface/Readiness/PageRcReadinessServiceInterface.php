<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Readiness;

use App\Paging\DTO\Readiness\PageRcReadinessReportDTO;

interface PageRcReadinessServiceInterface
{
    public function buildReport(): PageRcReadinessReportDTO;
}
