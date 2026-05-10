<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Editor;

interface PageContentSanitizerInterface
{
    public function sanitizeHtml(string $html): string;

    public function toPlainText(string $html): string;
}
