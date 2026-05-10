<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\Enum\PageExportFormat;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Export\PageExportServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/page/export')]
final class PageExportController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageExportServiceInterface $pageExportService,
    ) {
    }

    #[Route('/{code}.{format}', name: 'page_api_export', requirements: ['format' => 'html|json|md|markdown'], methods: ['GET'])]
    public function export(string $code, string $format): Response
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (null === $page || null === $page->getPublishedRevision()) {
            throw $this->createNotFoundException(sprintf('Published page "%s" was not found.', $code));
        }

        $exportFormat = match ($format) {
            'html' => PageExportFormat::Html,
            'json' => PageExportFormat::Json,
            'md', 'markdown' => PageExportFormat::Markdown,
            default => throw $this->createNotFoundException(sprintf('Unsupported page export format "%s".', $format)),
        };

        $view = $this->pageExportService->exportPublished($page, $exportFormat);

        return new Response($view->content, Response::HTTP_OK, [
            'Content-Type' => $view->contentType,
            'ETag' => '"'.$view->checksum.'"',
        ]);
    }
}
