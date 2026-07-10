<?php

declare(strict_types=1);

namespace App\Paging\Service\Handoff;

use App\Paging\DTO\Handoff\PageHandoffItem;
use App\Paging\DTO\Handoff\PageHandoffReport;
use App\Paging\ServiceInterface\Handoff\PageHandoffSummaryServiceInterface;

/**
 * Describes the final handoff boundary after the Paging RC waves.
 *
 * This is intentionally descriptive and local. It does not reach into
 * Backofficing, Interfacing, Navigating, Attachment, or host security layers.
 */
final class PageHandoffSummaryService implements PageHandoffSummaryServiceInterface
{
    public function buildReport(): PageHandoffReport
    {
        return new PageHandoffReport([
            new PageHandoffItem('runtime', 'ready', 'Standalone runtime and bundle entrypoint are present.'),
            new PageHandoffItem('model', 'ready', 'Page, revision, publication, attachment reference, grant, and acceptance entities are present.'),
            new PageHandoffItem('contracts', 'ready', 'Service interfaces, DTOs, bridge payloads, and API contracts are present.'),
            new PageHandoffItem('outputs', 'ready', 'HTML, Markdown, JSON, and bridge output surfaces are defined.'),
            new PageHandoffItem('security', 'ready', 'Local owner/grant/voter checks are present without owning host role hierarchy.'),
            new PageHandoffItem('admin_boundary', 'ready', 'Paging owns a service-driven EasyAdmin operator surface as the admin UI exception.'),
            new PageHandoffItem('navigation_boundary', 'ready', 'Paging exposes Navigating-compatible entrypoint metadata; Navigating owns shell placement.'),
            new PageHandoffItem('visual_boundary', 'ready', 'Paging owns bridge payloads; Interfacing owns visual shell and rendering composition.'),
            new PageHandoffItem('storage_boundary', 'ready', 'Attachment storage remains outside Paging; only references are owned here.'),
        ]);
    }
}
