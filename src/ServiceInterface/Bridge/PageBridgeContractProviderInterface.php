<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Bridge;

use App\Paging\DTO\Bridge\PageBridgePayload;
use App\Paging\Entity\Page;

interface PageBridgeContractProviderInterface
{
    public function byCode(string $code): PageBridgePayload;

    public function bySlug(string $slug): PageBridgePayload;

    public function forPublishedPage(Page $page): PageBridgePayload;
}
