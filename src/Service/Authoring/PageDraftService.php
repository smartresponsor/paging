<?php

declare(strict_types=1);

namespace App\Paging\Service\Authoring;

use App\Paging\DTO\Authoring\PageCreateInputDTO;
use App\Paging\DTO\Authoring\PageUpdateInputDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\RepositoryInterface\PageRepositoryInterface;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;

final readonly class PageDraftService implements PageDraftServiceInterface
{
    public function __construct(private PageRepositoryInterface $pageRepository)
    {
    }

    public function createPage(PageCreateInputDTO $input): Page
    {
        $page = new Page($input->code, $input->slug, $input->title, $input->kind, $input->ownerUserId);
        $this->pageRepository->save($page);

        return $page;
    }

    public function updatePage(Page $page, PageUpdateInputDTO $input): Page
    {
        $slug = '' === trim($input->slug) ? $page->getSlug() : $input->slug;
        $page->rename($input->title, $slug);
        $page->assignOwner($input->ownerUserId);
        $this->pageRepository->flush();

        return $page;
    }
}
