<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\Entity\Page;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Bridge\PageApiBridgePayloadFactoryInterface;
use App\Paging\ServiceInterface\Http\PageHttpPayloadFactoryInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/page')]
final class PageReadController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageRenderServiceInterface $pageRenderService,
        private readonly PageApiBridgePayloadFactoryInterface $pageBridgePayloadFactory,
        private readonly PageHttpPayloadFactoryInterface $pageHttpPayloadFactory,
    ) {
    }

    #[Route('/{code}', name: 'page_api_read', methods: ['GET'])]
    public function read(string $code): JsonResponse
    {
        $page = $this->findPublishedPage($code);

        return new JsonResponse([
            'page' => $this->pageHttpPayloadFactory->pageToArray($page),
            'render' => $this->pageHttpPayloadFactory->renderViewToArray($this->pageRenderService->renderPublished($page)),
        ]);
    }

    #[Route('/bridge/{code}', name: 'page_api_bridge', methods: ['GET'])]
    public function bridge(string $code): JsonResponse
    {
        $page = $this->findPublishedPage($code);

        return new JsonResponse([
            'bridge' => $this->pageHttpPayloadFactory->bridgePayloadToArray($this->pageBridgePayloadFactory->createForPublishedPage($page)),
        ]);
    }

    private function findPublishedPage(string $code): Page
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (null === $page || null === $page->getPublishedRevision()) {
            throw new NotFoundHttpException(sprintf('Published page "%s" was not found.', $code));
        }

        return $page;
    }
}
