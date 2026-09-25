<?php

declare(strict_types=1);

namespace App\Paging\Factory\Bridge;

use App\Paging\DTO\Bridge\PageBridgeAttachmentDTO;
use App\Paging\DTO\Bridge\PageBridgeLegalNoticeDTO;
use App\Paging\DTO\Bridge\PageBridgePayloadDTO;
use App\Paging\DTO\Bridge\PageBridgeRenderHintsDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Enum\PageKind;
use App\Paging\FactoryInterface\Bridge\PageBridgePayloadFactoryInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;

final readonly class PageBridgePayloadFactory implements PageBridgePayloadFactoryInterface
{
    public function __construct(private PageRenderServiceInterface $pageRenderService)
    {
    }

    public function createForPublishedPage(Page $page): PageBridgePayloadDTO
    {
        $revision = $page->getPublishedRevision();
        if (!$revision instanceof PageRevision) {
            throw new \RuntimeException(sprintf('Page "%s" does not have a published revision for bridge output.', $page->getCode()));
        }

        $view = $this->pageRenderService->renderPublished($page);
        $publication = $this->latestPublicationForRevision($page, $revision);
        $isLegal = PageKind::Policy === $page->getKind() || PageKind::Rule === $page->getKind();
        $attachments = [];

        foreach ($page->getAttachmentReferences() as $reference) {
            $attachments[] = new PageBridgeAttachmentDTO(
                $reference->getAttachmentId(),
                $reference->getUsage(),
                $reference->getAttachmentCode(),
                $reference->getPosition(),
            );
        }

        return new PageBridgePayloadDTO(
            $view->code,
            $view->slug,
            $view->title,
            $view->kind,
            $page->getStatus(),
            $view->version,
            $view->bodyHtml,
            $view->bodyText,
            $view->bodyMarkdown,
            $revision->getBodyJson(),
            $view->checksum,
            $attachments,
            new PageBridgeRenderHintsDTO(
                preferredTemplateKey: 'page/'.$page->getKind()->value,
                contentWidth: $isLegal ? 'legal' : 'standard',
                showTitle: true,
                showVersion: $isLegal,
                showUpdatedAt: true,
                showEffectiveDate: $isLegal,
                legalMode: $isLegal,
                allowTableOfContents: $isLegal,
            ),
            $isLegal ? new PageBridgeLegalNoticeDTO(
                sprintf('Version %d', $view->version),
                $view->effectiveFrom,
                $page->getUpdatedAt(),
                true,
                $view->version,
            ) : null,
            $view->publishedAt,
            $view->effectiveFrom,
            $publication?->getExpiresAt(),
            $page->getUpdatedAt(),
        );
    }

    private function latestPublicationForRevision(Page $page, PageRevision $revision): ?PagePublication
    {
        foreach ($page->getPublications() as $publication) {
            if ($publication->getRevision()->getId() === $revision->getId()) {
                return $publication;
            }
        }

        return null;
    }
}
