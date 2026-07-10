<?php

declare(strict_types=1);

namespace App\Paging\Service\Interfacing;

use App\Paging\Controller\Public\PageViewController;
use App\Paging\DTO\Bridge\PageBridgePayload;
use App\Paging\DTO\Interfacing\PageInterfacingContractReport;
use App\Paging\DTO\Rendering\PageRenderView;
use App\Paging\ServiceInterface\Bridge\PageBridgeContractProviderInterface;
use App\Paging\ServiceInterface\Interfacing\PageInterfacingContractServiceInterface;

final class PageInterfacingContractService implements PageInterfacingContractServiceInterface
{
    public function buildReport(): PageInterfacingContractReport
    {
        return new PageInterfacingContractReport(
            PageBridgeContractProviderInterface::class, PageBridgePayload::class, PageRenderView::class, PageViewController::class,
            ['code', 'slug', 'title', 'kind', 'status', 'revisionNumber', 'bodyHtml', 'bodyText', 'checksum', 'renderHints'],
            ['preferredTemplateKey', 'contentWidth', 'showTitle', 'showUpdatedAt', 'legalMode'], ['page_public_index', 'page_public_view'], ['@Interfacing/base.html.twig', 'templates/page/view.html.twig']
        );
    }
}
