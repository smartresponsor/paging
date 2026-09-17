<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Bridge;

use App\Paging\DTO\Bridge\PageApiBridgePayload;
use App\Paging\Entity\Page;

interface PageApiBridgePayloadFactoryInterface
{
    public function createForPublishedPage(Page $page): PageApiBridgePayload;
}
