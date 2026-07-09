<?php

declare(strict_types=1);

namespace App\Paging\Service\Authoring;

use App\Paging\DTO\Authoring\PageCreateInput;
use App\Paging\DTO\Authoring\PageUpdateInput;
use App\Paging\Entity\Page;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PageDraftService implements PageDraftServiceInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createPage(PageCreateInput $input): Page
    {
        $page = new Page($input->code, $input->slug, $input->title, $input->kind, $input->ownerUserId);
        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page;
    }

    public function updatePage(Page $page, PageUpdateInput $input): Page
    {
        $slug = '' === trim($input->slug) ? $page->getSlug() : $input->slug;
        $page->rename($input->title, $slug);
        $page->assignOwner($input->ownerUserId);
        $this->entityManager->flush();

        return $page;
    }
}
