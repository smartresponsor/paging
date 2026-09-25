<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\Enum\PageExportFormat;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Export\PageExportServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class PageExportController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageExportServiceInterface $pageExportService,
    ) {
    }

    #[Route('/page/export/{code}', name: 'page_api_export', methods: ['GET'])]
    public function export(string $code, Request $request): Response
    {
        $format = (string) $request->query->get('format', 'html');
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (null === $page || null === $page->getPublishedRevision()) {
            throw new NotFoundHttpException(sprintf('Published page "%s" was not found.', $code));
        }

        $exportFormat = match ($format) {
            'html' => PageExportFormat::Html,
            'json' => PageExportFormat::Json,
            'md', 'markdown' => PageExportFormat::Markdown,
            default => throw new NotFoundHttpException(sprintf('Unsupported page export format "%s".', $format)),
        };

        $view = $this->pageExportService->exportPublished($page, $exportFormat);

        return new Response($view->content, Response::HTTP_OK, [
            'Content-Type' => $view->contentType,
            'ETag' => '"'.$view->checksum.'"',
        ]);
    }
}
