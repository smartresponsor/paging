<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\DTO\Authoring\PageCreateInputDTO;
use App\Paging\DTO\Authoring\PageUpdateInputDTO;
use App\Paging\Enum\PageKind;
use App\Paging\FactoryInterface\Http\PageHttpPayloadFactoryInterface;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class PageAuthoringController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageDraftServiceInterface $pageDraftService,
        private readonly PageHttpPayloadFactoryInterface $pageHttpPayloadFactory,
    ) {
    }

    #[Route('/page/authoring/page', name: 'page_api_authoring_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $page = $this->pageDraftService->createPage(new PageCreateInputDTO(
            (string) ($payload['code'] ?? ''),
            (string) ($payload['slug'] ?? ''),
            (string) ($payload['title'] ?? ''),
            $this->kindFromPayload($payload['kind'] ?? null),
            isset($payload['ownerUserId']) ? (string) $payload['ownerUserId'] : null,
        ));

        return new JsonResponse(['page' => $this->pageHttpPayloadFactory->pageToArray($page)], 201);
    }

    #[Route('/page/authoring/page/{code}', name: 'page_api_authoring_update', methods: ['PATCH'])]
    public function update(string $code, Request $request): JsonResponse
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (null === $page) {
            throw new NotFoundHttpException(sprintf('Page "%s" was not found.', $code));
        }

        $payload = $this->jsonPayload($request);
        $page = $this->pageDraftService->updatePage($page, new PageUpdateInputDTO(
            (string) ($payload['title'] ?? $page->getTitle()),
            (string) ($payload['slug'] ?? $page->getSlug()),
            array_key_exists('ownerUserId', $payload) ? (is_string($payload['ownerUserId']) ? $payload['ownerUserId'] : null) : $page->getOwnerUserId(),
        ));

        return new JsonResponse(['page' => $this->pageHttpPayloadFactory->pageToArray($page)]);
    }

    /** @return array<string, mixed> */
    private function jsonPayload(Request $request): array
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return is_array($payload) ? $payload : [];
    }

    private function kindFromPayload(mixed $value): PageKind
    {
        return is_string($value) ? PageKind::from($value) : PageKind::Page;
    }
}
