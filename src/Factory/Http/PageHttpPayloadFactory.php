<?php

declare(strict_types=1);

namespace App\Paging\Factory\Http;

use App\Paging\DTO\Bridge\PageApiBridgePayloadDTO;
use App\Paging\DTO\Export\PageExportViewDTO;
use App\Paging\DTO\Rendering\PageRenderViewDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\FactoryInterface\Http\PageHttpPayloadFactoryInterface;

final class PageHttpPayloadFactory implements PageHttpPayloadFactoryInterface
{
    public function pageToArray(Page $page): array
    {
        return [
            'id' => $page->getId(),
            'code' => $page->getCode(),
            'slug' => $page->getSlug(),
            'title' => $page->getTitle(),
            'kind' => $page->getKind()->value,
            'status' => $page->getStatus()->value,
            'ownerUserId' => $page->getOwnerUserId(),
            'currentRevision' => $page->getCurrentRevision()?->getRevisionNumber(),
            'publishedRevision' => $page->getPublishedRevision()?->getRevisionNumber(),
            'createdAt' => $this->date($page->getCreatedAt()),
            'updatedAt' => $this->date($page->getUpdatedAt()),
        ];
    }

    public function revisionToArray(PageRevision $revision): array
    {
        return [
            'id' => $revision->getId(),
            'pageCode' => $revision->getPage()->getCode(),
            'revisionNumber' => $revision->getRevisionNumber(),
            'title' => $revision->getTitle(),
            'changeNote' => $revision->getChangeNote(),
            'checksum' => $revision->getChecksum(),
            'createdByUserId' => $revision->getCreatedByUserId(),
            'createdAt' => $this->date($revision->getCreatedAt()),
            'lockedAt' => $this->nullableDate($revision->getLockedAt()),
            'locked' => $revision->isLocked(),
        ];
    }

    public function publicationToArray(PagePublication $publication): array
    {
        return [
            'id' => $publication->getId(),
            'pageCode' => $publication->getPage()->getCode(),
            'revisionNumber' => $publication->getRevision()->getRevisionNumber(),
            'status' => $publication->getStatus()->value,
            'publishedAt' => $this->date($publication->getPublishedAt()),
            'effectiveFrom' => $this->nullableDate($publication->getEffectiveFrom()),
            'expiresAt' => $this->nullableDate($publication->getExpiresAt()),
            'publishedByUserId' => $publication->getPublishedByUserId(),
        ];
    }

    public function renderViewToArray(PageRenderViewDTO $view): array
    {
        return [
            'code' => $view->code,
            'slug' => $view->slug,
            'title' => $view->title,
            'kind' => $view->kind->value,
            'version' => $view->version,
            'bodyHtml' => $view->bodyHtml,
            'bodyText' => $view->bodyText,
            'bodyMarkdown' => $view->bodyMarkdown,
            'checksum' => $view->checksum,
            'publishedAt' => $this->nullableDate($view->publishedAt),
            'effectiveFrom' => $this->nullableDate($view->effectiveFrom),
        ];
    }

    public function bridgePayloadToArray(PageApiBridgePayloadDTO $payload): array
    {
        return [
            'code' => $payload->code,
            'slug' => $payload->slug,
            'title' => $payload->title,
            'kind' => $payload->kind->value,
            'version' => $payload->version,
            'bodyHtml' => $payload->bodyHtml,
            'bodyText' => $payload->bodyText,
            'checksum' => $payload->checksum,
            'attachments' => array_map(static fn (object $attachment): array => [
                'attachmentId' => $attachment->attachmentId,
                'usage' => $attachment->usage->value,
                'attachmentCode' => $attachment->attachmentCode,
                'position' => $attachment->position,
            ], $payload->attachments),
            'publishedAt' => $this->nullableDate($payload->publishedAt),
            'effectiveFrom' => $this->nullableDate($payload->effectiveFrom),
            'renderHints' => $payload->renderHints,
        ];
    }

    public function exportViewToArray(PageExportViewDTO $view): array
    {
        return [
            'code' => $view->code,
            'slug' => $view->slug,
            'title' => $view->title,
            'format' => $view->format->value,
            'contentType' => $view->contentType,
            'checksum' => $view->checksum,
        ];
    }

    private function date(\DateTimeImmutable $date): string
    {
        return $date->format(DATE_ATOM);
    }

    private function nullableDate(?\DateTimeImmutable $date): ?string
    {
        return $date?->format(DATE_ATOM);
    }
}
