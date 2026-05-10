<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Acceptance;

use App\Paging\DTO\Acceptance\PageAcceptanceInput;
use App\Paging\DTO\Acceptance\PageAcceptanceView;
use App\Paging\Entity\PageAcceptance;
use App\Paging\Entity\PageRevision;

interface PageAcceptanceServiceInterface
{
    public function accept(PageAcceptanceInput $input): PageAcceptance;

    public function hasAccepted(PageRevision $revision, string $subjectUserId): bool;

    public function view(PageAcceptance $acceptance): PageAcceptanceView;
}
