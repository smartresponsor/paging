<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Contract;

use App\Paging\DTO\Contract\PageBridgePayload;
use App\Paging\Entity\Page;

interface PageBridgePayloadFactoryInterface
{
    public function createForPublishedPage(Page $page): PageBridgePayload;
}
