<?php

declare(strict_types=1);

namespace App\Paging\Service\Publication;

use App\Paging\DTO\Publication\PagePublishInput;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PagePublicationService implements PagePublicationServiceInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function publishRevision(PageRevision $revision, PagePublishInput $input): PagePublication
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

        $this->entityManager->persist($publication);
        $this->entityManager->flush();

        return $publication;
    }
}
