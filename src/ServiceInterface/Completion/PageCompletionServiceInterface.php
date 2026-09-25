<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Completion;

use App\Paging\DTO\Completion\PageCompletionReportDTO;

interface PageCompletionServiceInterface
{
    public function buildReport(): PageCompletionReportDTO;
}
