<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Readiness;

use App\Paging\DTO\Readiness\PageRcReadinessReport;

interface PageRcReadinessServiceInterface
{
    public function buildReport(): PageRcReadinessReport;
}
