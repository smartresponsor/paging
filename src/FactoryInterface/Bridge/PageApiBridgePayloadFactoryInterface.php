<?php

declare(strict_types=1);

namespace App\Paging\FactoryInterface\Bridge;

use App\Paging\DTO\Bridge\PageApiBridgePayloadDTO;
use App\Paging\Entity\PageEntity as Page;

interface PageApiBridgePayloadFactoryInterface
{
    public function createForPublishedPage(Page $page): PageApiBridgePayloadDTO;
}
