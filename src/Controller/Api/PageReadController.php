<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\Entity\Page;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Contract\PageBridgePayloadFactoryInterface;
use App\Paging\ServiceInterface\Http\PageHttpPayloadFactoryInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/page/pages')]
final class PageReadController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageRenderServiceInterface $pageRenderService,
        private readonly PageBridgePayloadFactoryInterface $pageBridgePayloadFactory,
        private readonly PageHttpPayloadFactoryInterface $pageHttpPayloadFactory,
    ) {
    }

    #[Route('/{code}', name: 'page_api_read', methods: ['GET'])]
    public function read(string $code): JsonResponse
    {
        $page = $this->findPublishedPage($code);

        return $this->json([
            'page' => $this->pageHttpPayloadFactory->pageToArray($page),
            'render' => $this->pageHttpPayloadFactory->renderViewToArray($this->pageRenderService->renderPublished($page)),
        ]);
    }

    #[Route('/{code}/bridge', name: 'page_api_bridge', methods: ['GET'])]
    public function bridge(string $code): JsonResponse
    {
        $page = $this->findPublishedPage($code);

        return $this->json([
            'bridge' => $this->pageHttpPayloadFactory->bridgePayloadToArray($this->pageBridgePayloadFactory->createForPublishedPage($page)),
        ]);
    }

    private function findPublishedPage(string $code): Page
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (null === $page || null === $page->getPublishedRevision()) {
            throw $this->createNotFoundException(sprintf('Published page "%s" was not found.', $code));
        }

        return $page;
    }
}
