<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Security;

use App\Paging\DTO\Security\PageGrantCheck;
use App\Paging\DTO\Security\PageGrantInput;
use App\Paging\Entity\PageGrant;

interface PageGrantServiceInterface
{
    public function grant(PageGrantInput $input): PageGrant;

    public function isGranted(PageGrantCheck $check): bool;
}
