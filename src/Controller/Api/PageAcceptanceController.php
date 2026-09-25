<?php

declare(strict_types=1);

namespace App\Paging\Controller\Api;

use App\Paging\DTO\Acceptance\PageAcceptanceInputDTO;
use App\Paging\DTO\Acceptance\PageAcceptanceViewDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Acceptance\PageAcceptanceServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class PageAcceptanceController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageAcceptanceServiceInterface $pageAcceptanceService,
    ) {
    }

    #[Route('/page/acceptance/revision/{revisionNumber}', name: 'page_api_accept_revision', methods: ['POST'])]
    public function accept(int $revisionNumber, Request $request): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $code = $this->pageCodeFromRequest($request, $payload);
        $revision = $this->findRevision($code, $revisionNumber);
        $subjectUserId = (string) ($payload['subjectUserId'] ?? '');
        if ('' === $subjectUserId) {
            return new JsonResponse(['error' => 'subjectUserId is required.'], 422);
        }

        $acceptance = $this->pageAcceptanceService->accept(new PageAcceptanceInputDTO(
            $revision,
            $subjectUserId,
            $request->getClientIp(),
            $request->headers->get('User-Agent'),
            isset($payload['acceptanceContext']) && is_array($payload['acceptanceContext']) ? $payload['acceptanceContext'] : null,
        ));

        return new JsonResponse(['acceptance' => $this->viewToArray($this->pageAcceptanceService->view($acceptance))], 201);
    }

    #[Route('/page/acceptance/revision/{revisionNumber}/subject/{subjectUserId}', name: 'page_api_acceptance_check', methods: ['GET'])]
    public function check(string $subjectUserId, Request $request): JsonResponse
    {
        $code = $this->pageCodeFromRequest($request, []);
        $revisionNumber = $this->revisionNumberFromRequest($request);
        $revision = $this->findRevision($code, $revisionNumber);

        return new JsonResponse([
            'pageCode' => $revision->getPage()->getCode(),
            'revisionNumber' => $revision->getRevisionNumber(),
            'subjectUserId' => $subjectUserId,
            'accepted' => $this->pageAcceptanceService->hasAccepted($revision, $subjectUserId),
            'checksum' => $revision->getChecksum(),
        ]);
    }

    /** @param array<string, mixed> $payload */
    private function pageCodeFromRequest(Request $request, array $payload): string
    {
        $code = $payload['code'] ?? $request->query->get('code');
        if (!is_string($code) || '' === trim($code)) {
            throw new NotFoundHttpException('Page code is required.');
        }

        return $code;
    }

    private function revisionNumberFromRequest(Request $request): int
    {
        $revisionNumber = $request->attributes->get('revisionNumber') ?? $request->query->get('revisionNumber');
        if (is_numeric($revisionNumber) && (int) $revisionNumber > 0) {
            return (int) $revisionNumber;
        }

        throw new NotFoundHttpException('Page revision number is required.');
    }

    private function findRevision(string $code, int $revisionNumber): PageRevision
    {
        $page = $this->findPage($code);
        foreach ($page->getRevisions() as $revision) {
            if ($revision->getRevisionNumber() === $revisionNumber) {
                return $revision;
            }
        }

        throw new NotFoundHttpException(sprintf('Page "%s" revision %d was not found.', $code, $revisionNumber));
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
        if ('' === trim($request->getContent())) {
            return [];
        }

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return is_array($payload) ? $payload : [];
    }

    /** @return array<string, mixed> */
    private function viewToArray(PageAcceptanceViewDTO $view): array
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
