<?php

declare(strict_types=1);

namespace App\Paging\Enum;

enum PagePublicationStatus: string
{
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Expired = 'expired';
    case Withdrawn = 'withdrawn';
}
