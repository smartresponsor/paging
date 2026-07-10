<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Interfacing;

use App\Paging\DTO\Interfacing\PageInterfacingContractReport;

interface PageInterfacingContractServiceInterface
{
    public function buildReport(): PageInterfacingContractReport;
}
