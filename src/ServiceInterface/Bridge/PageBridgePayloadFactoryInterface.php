<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Bridge;

use App\Paging\DTO\Bridge\PageBridgePayload;
use App\Paging\Entity\Page;

interface PageBridgePayloadFactoryInterface
{
    public function createForPublishedPage(Page $page): PageBridgePayload;
}
