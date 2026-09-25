<?php

declare(strict_types=1);

namespace App\Paging\Factory\Bridge;

use App\Paging\DTO\Bridge\PageApiAttachmentReferenceViewDTO;
use App\Paging\DTO\Bridge\PageApiBridgePayloadDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\FactoryInterface\Bridge\PageApiBridgePayloadFactoryInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;

final readonly class PageApiBridgePayloadFactory implements PageApiBridgePayloadFactoryInterface
{
    public function __construct(private PageRenderServiceInterface $pageRenderService)
    {
    }

    public function createForPublishedPage(Page $page): PageApiBridgePayloadDTO
    {
        $view = $this->pageRenderService->renderPublished($page);
        $attachments = [];

        foreach ($page->getAttachmentReferences() as $reference) {
            $attachments[] = new PageApiAttachmentReferenceViewDTO(
                $reference->getAttachmentId(),
                $reference->getUsage(),
                $reference->getAttachmentCode(),
                $reference->getPosition(),
            );
        }

        return new PageApiBridgePayloadDTO(
            $view->code,
            $view->slug,
            $view->title,
            $view->kind,
            $view->version,
            $view->bodyHtml,
            $view->bodyText,
            $view->checksum,
            $attachments,
            $view->publishedAt,
            $view->effectiveFrom,
            [
                'show_updated_at' => true,
                'show_version' => 'policy' === $page->getKind()->value || 'rule' === $page->getKind()->value,
                'preferred_template_key' => 'page/'.$page->getKind()->value,
            ],
        );
    }
}
