<?php

declare(strict_types=1);

namespace App\Paging\Normalizer\Editor;

use App\Paging\DTO\Editor\PageEditorPayloadDTO;
use App\Paging\NormalizerInterface\Editor\PageEditorPayloadNormalizerInterface;
use App\Paging\ServiceInterface\Editor\PageContentSanitizerInterface;

final readonly class PageEditorPayloadNormalizer implements PageEditorPayloadNormalizerInterface
{
    public function __construct(private PageContentSanitizerInterface $sanitizer)
    {
    }

    public function normalize(string $title, string $bodyHtml, ?string $bodyMarkdown = null, ?array $bodyJson = null): PageEditorPayloadDTO
    {
        $cleanHtml = $this->sanitizer->sanitizeHtml($bodyHtml);

        return new PageEditorPayloadDTO(
            title: trim($title),
            bodyHtml: $cleanHtml,
            bodyText: $this->sanitizer->toPlainText($cleanHtml),
            bodyMarkdown: null !== $bodyMarkdown ? trim($bodyMarkdown) : null,
            bodyJson: $bodyJson,
        );
    }
}
