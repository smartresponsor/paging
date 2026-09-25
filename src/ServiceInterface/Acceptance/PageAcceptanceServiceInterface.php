<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Acceptance;

use App\Paging\DTO\Acceptance\PageAcceptanceInputDTO;
use App\Paging\DTO\Acceptance\PageAcceptanceViewDTO;
use App\Paging\Entity\PageAcceptanceEntity as PageAcceptance;
use App\Paging\Entity\PageRevisionEntity as PageRevision;

interface PageAcceptanceServiceInterface
{
    public function accept(PageAcceptanceInputDTO $input): PageAcceptance;

    public function hasAccepted(PageRevision $revision, string $subjectUserId): bool;

    public function view(PageAcceptance $acceptance): PageAcceptanceViewDTO;
}
