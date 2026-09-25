<?php

declare(strict_types=1);

namespace App\Paging\ProviderInterface\Bridge;

use App\Paging\DTO\Bridge\PageBridgePayloadDTO;
use App\Paging\Entity\PageEntity as Page;

interface PageBridgeContractProviderInterface
{
    public function byCode(string $code): PageBridgePayloadDTO;

    public function bySlug(string $slug): PageBridgePayloadDTO;

    public function forPublishedPage(Page $page): PageBridgePayloadDTO;
}
