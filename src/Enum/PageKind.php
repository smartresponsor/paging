<?php

declare(strict_types=1);

namespace App\Paging\Enum;

enum PageKind: string
{
    case Page = 'page';
    case Policy = 'policy';
    case Rule = 'rule';
    case Blog = 'blog';
    case Help = 'help';
    case System = 'system';
}
