<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Security;

use App\Paging\DTO\Security\PageSecurityContractReport;

interface PageSecurityContractServiceInterface
{
    public function buildReport(): PageSecurityContractReport;
}
