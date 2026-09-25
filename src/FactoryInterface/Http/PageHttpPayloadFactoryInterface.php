<?php

declare(strict_types=1);

namespace App\Paging\FactoryInterface\Http;

use App\Paging\DTO\Bridge\PageApiBridgePayloadDTO;
use App\Paging\DTO\Export\PageExportViewDTO;
use App\Paging\DTO\Rendering\PageRenderViewDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;

interface PageHttpPayloadFactoryInterface
{
    /** @return array<string, mixed> */
    public function pageToArray(Page $page): array;

    /** @return array<string, mixed> */
    public function revisionToArray(PageRevision $revision): array;

    /** @return array<string, mixed> */
    public function publicationToArray(PagePublication $publication): array;

    /** @return array<string, mixed> */
    public function renderViewToArray(PageRenderViewDTO $view): array;

    /** @return array<string, mixed> */
    public function bridgePayloadToArray(PageApiBridgePayloadDTO $payload): array;

    /** @return array<string, mixed> */
    public function exportViewToArray(PageExportViewDTO $view): array;
}
