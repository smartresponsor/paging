<?php

declare(strict_types=1);

namespace App\Paging\Service\Bridge;

use App\Paging\DTO\Bridge\PageBridgePayload;
use App\Paging\Entity\Page;
use App\Paging\Repository\PageRepository;
use App\Paging\ServiceInterface\Bridge\PageBridgeContractProviderInterface;
use App\Paging\ServiceInterface\Bridge\PageBridgePayloadFactoryInterface;
use App\Paging\ValueObject\PageSlug;

final readonly class PageBridgeContractProvider implements PageBridgeContractProviderInterface
{
    public function __construct(
        private PageRepository $pageRepository,
        private PageBridgePayloadFactoryInterface $pageBridgePayloadFactory,
    ) {
    }

    public function byCode(string $code): PageBridgePayload
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (!$page instanceof Page) {
            throw new \RuntimeException(sprintf('Page with code "%s" was not found for bridge output.', $code));
        }

        return $this->forPublishedPage($page);
    }

    public function bySlug(string $slug): PageBridgePayload
    {
        $page = $this->pageRepository->findOneBy(['slug' => PageSlug::fromSource($slug)->value()]);
        if (!$page instanceof Page) {
            throw new \RuntimeException(sprintf('Page with slug "%s" was not found for bridge output.', $slug));
        }

        return $this->forPublishedPage($page);
    }

    public function forPublishedPage(Page $page): PageBridgePayload
    {
        return $this->pageBridgePayloadFactory->createForPublishedPage($page);
    }
}
