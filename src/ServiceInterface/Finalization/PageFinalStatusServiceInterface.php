<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Finalization;

use App\Paging\DTO\Finalization\PageFinalStatusReport;

interface PageFinalStatusServiceInterface
{
    public function buildReport(): PageFinalStatusReport;
}
