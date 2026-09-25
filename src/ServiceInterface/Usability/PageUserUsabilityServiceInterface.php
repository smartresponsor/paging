<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Usability;

use App\Paging\DTO\Usability\PageUserUsabilityReportDTO;

interface PageUserUsabilityServiceInterface
{
    public function buildReport(): PageUserUsabilityReportDTO;
}
