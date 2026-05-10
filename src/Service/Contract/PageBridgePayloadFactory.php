<?php

declare(strict_types=1);

namespace App\Paging\Service\Contract;

use App\Paging\DTO\Contract\PageAttachmentReferenceView;
use App\Paging\DTO\Contract\PageBridgePayload;
use App\Paging\Entity\Page;
use App\Paging\ServiceInterface\Contract\PageBridgePayloadFactoryInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;

final readonly class PageBridgePayloadFactory implements PageBridgePayloadFactoryInterface
{
    public function __construct(private PageRenderServiceInterface $pageRenderService)
    {
    }

    public function createForPublishedPage(Page $page): PageBridgePayload
    {
        $view = $this->pageRenderService->renderPublished($page);
        $attachments = [];

        foreach ($page->getAttachmentReferences() as $reference) {
            $attachments[] = new PageAttachmentReferenceView(
                $reference->getAttachmentId(),
                $reference->getUsage(),
                $reference->getAttachmentCode(),
                $reference->getPosition(),
            );
        }

        return new PageBridgePayload(
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
