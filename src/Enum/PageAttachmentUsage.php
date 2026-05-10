<?php

declare(strict_types=1);

namespace App\Paging\Enum;

enum PageAttachmentUsage: string
{
    case Inline = 'inline';
    case Cover = 'cover';
    case Download = 'download';
    case LegalSupport = 'legal_support';
    case Embedded = 'embedded';
}
