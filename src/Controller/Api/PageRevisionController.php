<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\DTO\Revision\PageRevisionCreateInput;
use App\Paging\Entity\Page;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Http\PageHttpPayloadFactoryInterface;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/page/revision')]
final class PageRevisionController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageRevisionServiceInterface $pageRevisionService,
        private readonly PageHttpPayloadFactoryInterface $pageHttpPayloadFactory,
    ) {
    }

    #[Route('/{code}', name: 'page_api_revisions', methods: ['GET'])]
    public function list(string $code): JsonResponse
    {
        $page = $this->findPage($code);
        $revisions = [];
        foreach ($page->getRevisions() as $revision) {
            $revisions[] = $this->pageHttpPayloadFactory->revisionToArray($revision);
        }

        return new JsonResponse([
            'page' => $this->pageHttpPayloadFactory->pageToArray($page),
            'revisions' => $revisions,
        ]);
    }

    #[Route('/{code}', name: 'page_api_revision_create', methods: ['POST'])]
    public function create(string $code, Request $request): JsonResponse
    {
        $page = $this->findPage($code);
        $payload = $this->jsonPayload($request);

        $revision = $this->pageRevisionService->createRevision($page, new PageRevisionCreateInput(
            (string) ($payload['title'] ?? $page->getTitle()),
            (string) ($payload['bodyHtml'] ?? ''),
            isset($payload['bodyText']) ? (string) $payload['bodyText'] : null,
            isset($payload['bodyMarkdown']) ? (string) $payload['bodyMarkdown'] : null,
            isset($payload['bodyJson']) && is_array($payload['bodyJson']) ? $payload['bodyJson'] : null,
            isset($payload['changeNote']) ? (string) $payload['changeNote'] : null,
            isset($payload['createdByUserId']) ? (string) $payload['createdByUserId'] : null,
        ));

        return new JsonResponse(['revision' => $this->pageHttpPayloadFactory->revisionToArray($revision)], 201);
    }

    private function findPage(string $code): Page
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (null === $page) {
            throw new NotFoundHttpException(sprintf('Page "%s" was not found.', $code));
        }

        return $page;
    }

    /** @return array<string, mixed> */
    private function jsonPayload(Request $request): array
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return is_array($payload) ? $payload : [];
    }
}
