<?php

declare(strict_types=1);

namespace App\Paging\Service\Bridge;

use App\Paging\DTO\Bridge\PageApiAttachmentReferenceView;
use App\Paging\DTO\Bridge\PageApiBridgePayload;
use App\Paging\Entity\Page;
use App\Paging\ServiceInterface\Bridge\PageApiBridgePayloadFactoryInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;

final readonly class PageApiBridgePayloadFactory implements PageApiBridgePayloadFactoryInterface
{
    public function __construct(private PageRenderServiceInterface $pageRenderService)
    {
    }

    public function createForPublishedPage(Page $page): PageApiBridgePayload
    {
        $view = $this->pageRenderService->renderPublished($page);
        $attachments = [];

        foreach ($page->getAttachmentReferences() as $reference) {
            $attachments[] = new PageApiAttachmentReferenceView(
                $reference->getAttachmentId(),
                $reference->getUsage(),
                $reference->getAttachmentCode(),
                $reference->getPosition(),
            );
        }

        return new PageApiBridgePayload(
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
