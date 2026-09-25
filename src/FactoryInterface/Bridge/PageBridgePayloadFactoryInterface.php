<?php

declare(strict_types=1);

namespace App\Paging\FactoryInterface\Bridge;

use App\Paging\DTO\Bridge\PageBridgePayloadDTO;
use App\Paging\Entity\PageEntity as Page;

interface PageBridgePayloadFactoryInterface
{
    public function createForPublishedPage(Page $page): PageBridgePayloadDTO;
}
