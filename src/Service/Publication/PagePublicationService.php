<?php

declare(strict_types=1);

namespace App\Paging\Service\Publication;

use App\Paging\DTO\Publication\PagePublishInputDTO;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\RepositoryInterface\PagePublicationRepositoryInterface;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;

final readonly class PagePublicationService implements PagePublicationServiceInterface
{
    public function __construct(private PagePublicationRepositoryInterface $pagePublicationRepository)
    {
    }

    public function publishRevision(PageRevision $revision, PagePublishInputDTO $input): PagePublication
    {
        $revision->lock();
        $revision->getPage()->markPublished($revision);

        $publication = new PagePublication(
            $revision->getPage(),
            $revision,
            $input->effectiveFrom,
            $input->expiresAt,
            $input->publishedByUserId,
        );

        $this->pagePublicationRepository->save($publication);

        return $publication;
    }
}
