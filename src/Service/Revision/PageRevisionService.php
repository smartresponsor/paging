<?php

declare(strict_types=1);

namespace App\Paging\Service\Revision;

use App\Paging\DTO\Revision\PageRevisionCreateInput;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageRevision;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PageRevisionService implements PageRevisionServiceInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createRevision(Page $page, PageRevisionCreateInput $input): PageRevision
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
        $this->entityManager->persist($revision);
        $this->entityManager->flush();

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
