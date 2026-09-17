<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Http;

use App\Paging\DTO\Bridge\PageApiBridgePayload;
use App\Paging\DTO\Export\PageExportView;
use App\Paging\DTO\Rendering\PageRenderView;
use App\Paging\Entity\Page;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;

interface PageHttpPayloadFactoryInterface
{
    /** @return array<string, mixed> */
    public function pageToArray(Page $page): array;

    /** @return array<string, mixed> */
    public function revisionToArray(PageRevision $revision): array;

    /** @return array<string, mixed> */
    public function publicationToArray(PagePublication $publication): array;

    /** @return array<string, mixed> */
    public function renderViewToArray(PageRenderView $view): array;

    /** @return array<string, mixed> */
    public function bridgePayloadToArray(PageApiBridgePayload $payload): array;

    /** @return array<string, mixed> */
    public function exportViewToArray(PageExportView $view): array;
}
