<?php

declare(strict_types=1);

namespace App\Paging\Service\Editor;

use App\Paging\DTO\Editor\PageEditorPayload;
use App\Paging\ServiceInterface\Editor\PageContentSanitizerInterface;
use App\Paging\ServiceInterface\Editor\PageEditorPayloadNormalizerInterface;

final readonly class PageEditorPayloadNormalizer implements PageEditorPayloadNormalizerInterface
{
    public function __construct(private PageContentSanitizerInterface $sanitizer)
    {
    }

    public function normalize(string $title, string $bodyHtml, ?string $bodyMarkdown = null, ?array $bodyJson = null): PageEditorPayload
    {
        $cleanHtml = $this->sanitizer->sanitizeHtml($bodyHtml);

        return new PageEditorPayload(
            title: trim($title),
            bodyHtml: $cleanHtml,
            bodyText: $this->sanitizer->toPlainText($cleanHtml),
            bodyMarkdown: null !== $bodyMarkdown ? trim($bodyMarkdown) : null,
            bodyJson: $bodyJson,
        );
    }
}
