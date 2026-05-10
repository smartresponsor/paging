<?php

declare(strict_types=1);

namespace App\Paging\Enum;

enum PageExportFormat: string
{
    case Html = 'html';
    case Markdown = 'markdown';
    case Json = 'json';
}
