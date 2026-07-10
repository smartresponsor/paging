<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Usability;

use App\Paging\DTO\Usability\PageUserUsabilityReport;

interface PageUserUsabilityServiceInterface
{
    public function buildReport(): PageUserUsabilityReport;
}
