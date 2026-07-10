<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Navigation;

use App\Paging\DTO\Navigation\PageNavigationContractReport;

interface PageNavigationContractServiceInterface
{
    public function buildReport(): PageNavigationContractReport;
}
