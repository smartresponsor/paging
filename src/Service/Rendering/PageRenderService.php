<?php

declare(strict_types=1);

namespace App\Paging\Service\Rendering;

use App\Paging\DTO\Rendering\PageRenderViewDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;

final class PageRenderService implements PageRenderServiceInterface
{
    public function renderPublished(Page $page): PageRenderViewDTO
    {
        $revision = $page->getPublishedRevision();
        if (!$revision instanceof PageRevision) {
            throw new \RuntimeException(sprintf('Page "%s" does not have a published revision.', $page->getCode()));
        }

        return $this->createView($revision, $this->latestPublicationForRevision($page, $revision));
    }

    public function renderRevision(PageRevision $revision): PageRenderViewDTO
    {
        return $this->createView($revision, $this->latestPublicationForRevision($revision->getPage(), $revision));
    }

    private function createView(PageRevision $revision, ?PagePublication $publication): PageRenderViewDTO
    {
        $page = $revision->getPage();

        return new PageRenderViewDTO(
            $page->getCode(),
            $page->getSlug(),
            $revision->getTitle(),
            $page->getKind(),
            $revision->getRevisionNumber(),
            $revision->getBodyHtml(),
            $revision->getBodyText(),
            $revision->getBodyMarkdown(),
            $revision->getChecksum(),
            $publication?->getPublishedAt(),
            $publication?->getEffectiveFrom(),
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
