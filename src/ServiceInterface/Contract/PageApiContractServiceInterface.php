<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Contract;

use App\Paging\DTO\Contract\PageApiContractReport;

interface PageApiContractServiceInterface
{
    public function buildReport(): PageApiContractReport;
}
