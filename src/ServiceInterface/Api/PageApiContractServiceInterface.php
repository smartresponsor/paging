<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Api;

use App\Paging\DTO\Api\PageApiContractReportDTO;

interface PageApiContractServiceInterface
{
    public function buildReport(): PageApiContractReportDTO;
}
