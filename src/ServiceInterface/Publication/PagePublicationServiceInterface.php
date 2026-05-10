<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Publication;

use App\Paging\DTO\Publication\PagePublishInput;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;

interface PagePublicationServiceInterface
{
    public function publishRevision(PageRevision $revision, PagePublishInput $input): PagePublication;
}
