<?php

declare(strict_types=1);

namespace App\Paging\Controller\Public;

use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;
use App\Paging\ValueObject\PageSlug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class PageViewController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageRenderServiceInterface $pageRenderService,
    ) {
    }

    /** @return array<string, mixed> */
    #[Route('/page/', name: 'page_public_index', methods: ['GET'])]
    public function index(): array
    {
        return [
            '_view' => [
                'surface' => 'page',
                'operation' => 'index',
                'component' => 'Paging',
                'intent' => 'surface',
            ],
            'locations' => [],
            'data' => [
                'resourcePath' => '/page/{slug}',
            ],
            'meta' => [
                'source' => 'paging_public_boundary',
            ],
        ];
    }

    /** @return array<string, mixed> */
    #[Route('/page/{slug}', name: 'page_public_view', methods: ['GET'])]
    public function __invoke(string $slug): array
    {
        $page = $this->pageRepository->findOneBy(['slug' => PageSlug::fromSource($slug)->value()]);
        if (null === $page || null === $page->getPublishedRevision()) {
            throw new NotFoundHttpException(sprintf('Published page "%s" was not found.', $slug));
        }

        return [
            '_view' => [
                'surface' => 'page',
                'operation' => 'view',
                'component' => 'Paging',
                'intent' => 'surface',
            ],
            'locations' => [],
            'data' => [
                'page' => $page,
                'view' => $this->pageRenderService->renderPublished($page),
            ],
            'meta' => [
                'source' => 'paging_public_boundary',
            ],
        ];
    }
}
