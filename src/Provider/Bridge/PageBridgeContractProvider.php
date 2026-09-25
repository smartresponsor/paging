<?php

declare(strict_types=1);

namespace App\Paging\Provider\Bridge;

use App\Paging\DTO\Bridge\PageBridgePayloadDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\FactoryInterface\Bridge\PageBridgePayloadFactoryInterface;
use App\Paging\ProviderInterface\Bridge\PageBridgeContractProviderInterface;
use App\Paging\Repository\PageRepository;
use App\Paging\ValueObject\PageSlug;

final readonly class PageBridgeContractProvider implements PageBridgeContractProviderInterface
{
    public function __construct(
        private PageRepository $pageRepository,
        private PageBridgePayloadFactoryInterface $pageBridgePayloadFactory,
    ) {
    }

    public function byCode(string $code): PageBridgePayloadDTO
    {
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (!$page instanceof Page) {
            throw new \RuntimeException(sprintf('Page with code "%s" was not found for bridge output.', $code));
        }

        return $this->forPublishedPage($page);
    }

    public function bySlug(string $slug): PageBridgePayloadDTO
    {
        $page = $this->pageRepository->findOneBy(['objectIdentity.slug' => PageSlug::fromSource($slug)->value()]);
        if (!$page instanceof Page) {
            throw new \RuntimeException(sprintf('Page with slug "%s" was not found for bridge output.', $slug));
        }

        return $this->forPublishedPage($page);
    }

    public function forPublishedPage(Page $page): PageBridgePayloadDTO
    {
        return $this->pageBridgePayloadFactory->createForPublishedPage($page);
    }
}
