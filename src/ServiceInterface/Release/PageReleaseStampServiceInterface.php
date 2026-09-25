<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Release;

use App\Paging\DTO\Release\PageReleaseStampReportDTO;

interface PageReleaseStampServiceInterface
{
    public function buildReport(): PageReleaseStampReportDTO;
}
