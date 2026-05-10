<?php

declare(strict_types=1);

namespace App\Paging\Enum;

enum PageGrantType: string
{
    case View = 'view';
    case Edit = 'edit';
    case Publish = 'publish';
    case Own = 'own';
    case Manage = 'manage';
}
