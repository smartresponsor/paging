<?php

declare(strict_types=1);

namespace App\Paging\Service\Revision;

use App\Paging\DTO\Revision\PageRevisionCreateInputDTO;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\RepositoryInterface\PageRevisionRepositoryInterface;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;

final readonly class PageRevisionService implements PageRevisionServiceInterface
{
    public function __construct(private PageRevisionRepositoryInterface $pageRevisionRepository)
    {
    }

    public function createRevision(Page $page, PageRevisionCreateInputDTO $input): PageRevision
    {
        $revision = new PageRevision(
            $page,
            $this->nextRevisionNumber($page),
            $input->title,
            $input->bodyHtml,
            $input->bodyText ?? trim(strip_tags($input->bodyHtml)),
            $input->bodyMarkdown,
            $input->bodyJson,
            $input->changeNote,
            $input->createdByUserId,
        );

        $page->useCurrentRevision($revision);
        $this->pageRevisionRepository->save($revision);

        return $revision;
    }

    private function nextRevisionNumber(Page $page): int
    {
        $highest = 0;
        foreach ($page->getRevisions() as $revision) {
            $highest = max($highest, $revision->getRevisionNumber());
        }

        return $highest + 1;
    }
}
