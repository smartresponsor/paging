<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\DTO\Publication\PagePublishInput;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageRevision;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Http\PageHttpPayloadFactoryInterface;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/page/pages/{code}/publications')]
final class PagePublicationController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PagePublicationServiceInterface $pagePublicationService,
        private readonly PageHttpPayloadFactoryInterface $pageHttpPayloadFactory,
    ) {
    }

    #[Route('', name: 'page_api_publications', methods: ['GET'])]
    public function list(string $code): JsonResponse
    {
        $page = $this->findPage($code);
        $publications = [];
        foreach ($page->getPublications() as $publication) {
            $publications[] = $this->pageHttpPayloadFactory->publicationToArray($publication);
        }

        return $this->json([
            'page' => $this->pageHttpPayloadFactory->pageToArray($page),
            'publications' => $publications,
        ]);
    }

    #[Route('/revision/{revisionNumber}', name: 'page_api_publish_revision', methods: ['POST'])]
    public function publish(string $code, int $revisionNumber, Request $request): JsonResponse
    {
        $revision = $this->findRevision($code, $revisionNumber);
        $payload = $this->jsonPayload($request);

        $publication = $this->pagePublicationService->publishRevision($revision, new PagePublishInput(
            $this->dateFromPayload($payload['effectiveFrom'] ?? null),
            $this->dateFromPayload($payload['expiresAt'] ?? null),
            isset($payload['publishedByUserId']) ? (string) $payload['publishedByUserId'] : null,
        ));

        return $this->json([
            'page' => $this->pageHttpPayloadFactory->pageToArray($publication->getPage()),
            'publication' => $this->pageHttpPayloadFactory->publicationToArray($publication),
        ], 201);
    }

    private function findRevision(string $code, int $revisionNumber): PageRevision
    {
        $page = $this->findPage($code);
        foreach ($page->getRevisions() as $revision) {
            if ($revision->getRevisionNumber() === $revisionNumber) {
                return $revision;
            }
        }

        throw $this->createNotFoundException(sprintf('Page "%s" revision %d was not found.', $code, $revisionNumber));
    }

    private function findPage(string $code): Page
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (null === $page) {
            throw $this->createNotFoundException(sprintf('Page "%s" was not found.', $code));
        }

        return $page;
    }

    /** @return array<string, mixed> */
    private function jsonPayload(Request $request): array
    {
        if ('' === trim($request->getContent())) {
            return [];
        }

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return is_array($payload) ? $payload : [];
    }

    private function dateFromPayload(mixed $value): ?\DateTimeImmutable
    {
        if (!is_string($value) || '' === trim($value)) {
            return null;
        }

        return new \DateTimeImmutable($value);
    }
}
