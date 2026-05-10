<?php

declare(strict_types=1);

namespace App\Paging\DTO\Bridge;

use App\Paging\Enum\PageKind;
use App\Paging\Enum\PageStatus;

final readonly class PageBridgePayload
{
    /** @param list<PageBridgeAttachment> $attachments */
    public function __construct(
        public string $code,
        public string $slug,
        public string $title,
        public PageKind $kind,
        public PageStatus $status,
        public int $revisionNumber,
        public string $bodyHtml,
        public string $bodyText,
        public ?string $bodyMarkdown,
        public ?array $bodyJson,
        public string $checksum,
        public array $attachments,
        public PageBridgeRenderHints $renderHints,
        public ?PageBridgeLegalNotice $legalNotice = null,
        public ?\DateTimeImmutable $publishedAt = null,
        public ?\DateTimeImmutable $effectiveFrom = null,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'slug' => $this->slug,
            'title' => $this->title,
            'kind' => $this->kind->value,
            'status' => $this->status->value,
            'revisionNumber' => $this->revisionNumber,
            'bodyHtml' => $this->bodyHtml,
            'bodyText' => $this->bodyText,
            'bodyMarkdown' => $this->bodyMarkdown,
            'bodyJson' => $this->bodyJson,
            'checksum' => $this->checksum,
            'attachments' => array_map(static fn (PageBridgeAttachment $attachment): array => $attachment->toArray(), $this->attachments),
            'renderHints' => $this->renderHints->toArray(),
            'legalNotice' => $this->legalNotice?->toArray(),
            'publishedAt' => $this->publishedAt?->format(DATE_ATOM),
            'effectiveFrom' => $this->effectiveFrom?->format(DATE_ATOM),
            'expiresAt' => $this->expiresAt?->format(DATE_ATOM),
            'updatedAt' => $this->updatedAt?->format(DATE_ATOM),
        ];
    }
}
