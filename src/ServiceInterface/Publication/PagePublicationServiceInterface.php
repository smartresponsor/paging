<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Publication;

use App\Paging\DTO\Publication\PagePublishInputDTO;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;

interface PagePublicationServiceInterface
{
    public function publishRevision(PageRevision $revision, PagePublishInputDTO $input): PagePublication;
}
