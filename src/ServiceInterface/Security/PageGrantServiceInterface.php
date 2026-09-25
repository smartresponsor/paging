<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Security;

use App\Paging\DTO\Security\PageGrantCheckDTO;
use App\Paging\DTO\Security\PageGrantInputDTO;
use App\Paging\Entity\PageGrantEntity as PageGrant;

interface PageGrantServiceInterface
{
    public function grant(PageGrantInputDTO $input): PageGrant;

    public function isGranted(PageGrantCheckDTO $check): bool;
}
