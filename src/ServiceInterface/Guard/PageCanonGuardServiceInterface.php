<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Guard;

use App\Paging\DTO\Guard\PageCanonGuardReport;

interface PageCanonGuardServiceInterface
{
    public function buildReport(): PageCanonGuardReport;
}
