<?php

declare(strict_types=1);

namespace App\Paging\Controller\Public;

use App\Paging\ServiceInterface\Runtime\PageRuntimeProbeServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PageHealthController
{
    #[Route('/_page/health', name: 'page_health', methods: ['GET'])]
    public function __invoke(PageRuntimeProbeServiceInterface $runtimeProbeService): JsonResponse
    {
        return new JsonResponse($runtimeProbeService->snapshot());
    }
}
