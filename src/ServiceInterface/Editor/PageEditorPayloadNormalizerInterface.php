<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Editor;

use App\Paging\DTO\Editor\PageEditorPayload;

interface PageEditorPayloadNormalizerInterface
{
    /**
     * @param array<string, mixed>|null $bodyJson
     */
    public function normalize(string $title, string $bodyHtml, ?string $bodyMarkdown = null, ?array $bodyJson = null): PageEditorPayload;
}
