<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\DTO\Acceptance\PageAcceptanceInput;
use App\Paging\DTO\Acceptance\PageAcceptanceView;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageRevision;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Acceptance\PageAcceptanceServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/page/pages/{code}/acceptance')]
final class PageAcceptanceController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageAcceptanceServiceInterface $pageAcceptanceService,
    ) {
    }

    #[Route('/revision/{revisionNumber}', name: 'page_api_accept_revision', methods: ['POST'])]
    public function accept(string $code, int $revisionNumber, Request $request): JsonResponse
    {
        $revision = $this->findRevision($code, $revisionNumber);
        $payload = $this->jsonPayload($request);
        $subjectUserId = (string) ($payload['subjectUserId'] ?? '');
        if ('' === $subjectUserId) {
            return $this->json(['error' => 'subjectUserId is required.'], 422);
        }

        $acceptance = $this->pageAcceptanceService->accept(new PageAcceptanceInput(
            $revision,
            $subjectUserId,
            $request->getClientIp(),
            $request->headers->get('User-Agent'),
            isset($payload['acceptanceContext']) && is_array($payload['acceptanceContext']) ? $payload['acceptanceContext'] : null,
        ));

        return $this->json(['acceptance' => $this->viewToArray($this->pageAcceptanceService->view($acceptance))], 201);
    }

    #[Route('/revision/{revisionNumber}/subject/{subjectUserId}', name: 'page_api_acceptance_check', methods: ['GET'])]
    public function check(string $code, int $revisionNumber, string $subjectUserId): JsonResponse
    {
        $revision = $this->findRevision($code, $revisionNumber);

        return $this->json([
            'pageCode' => $revision->getPage()->getCode(),
            'revisionNumber' => $revision->getRevisionNumber(),
            'subjectUserId' => $subjectUserId,
            'accepted' => $this->pageAcceptanceService->hasAccepted($revision, $subjectUserId),
            'checksum' => $revision->getChecksum(),
        ]);
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

    private function viewToArray(PageAcceptanceView $view): array
    {
        return [
            'id' => $view->id,
            'pageCode' => $view->pageCode,
            'revisionNumber' => $view->revisionNumber,
            'subjectUserId' => $view->subjectUserId,
            'revisionChecksum' => $view->revisionChecksum,
            'acceptedAt' => $view->acceptedAt->format(DATE_ATOM),
            'ipHash' => $view->ipHash,
            'userAgentHash' => $view->userAgentHash,
            'acceptanceContext' => $view->acceptanceContext,
        ];
    }
}
