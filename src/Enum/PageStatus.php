<?php

declare(strict_types=1);

namespace App\Paging\Enum;

enum PageStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
