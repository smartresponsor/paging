<?php

declare(strict_types=1);

namespace App\Paging\Service\Export;

use App\Paging\DTO\Export\PageExportViewDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Enum\PageExportFormat;
use App\Paging\ServiceInterface\Export\PageExportServiceInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;

final readonly class PageExportService implements PageExportServiceInterface
{
    public function __construct(private PageRenderServiceInterface $pageRenderService)
    {
    }

    public function exportPublished(Page $page, PageExportFormat $format): PageExportViewDTO
    {
        $view = $this->pageRenderService->renderPublished($page);

        return match ($format) {
            PageExportFormat::Html => new PageExportViewDTO($view->code, $view->slug, $view->title, $format, $view->bodyHtml, 'text/html; charset=UTF-8', $view->checksum),
            PageExportFormat::Markdown => new PageExportViewDTO($view->code, $view->slug, $view->title, $format, $view->bodyMarkdown ?? $view->bodyText, 'text/markdown; charset=UTF-8', $view->checksum),
            PageExportFormat::Json => new PageExportViewDTO($view->code, $view->slug, $view->title, $format, $this->encodeJson($view), 'application/json; charset=UTF-8', $view->checksum),
        };
    }

    private function encodeJson(object $view): string
    {
        return json_encode($view, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
