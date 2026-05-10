<?php

declare(strict_types=1);

namespace App\Paging\DTO\Bridge;

final readonly class PageBridgeRenderHints
{
    public function __construct(
        public string $preferredTemplateKey = 'page/default',
        public string $contentWidth = 'standard',
        public bool $showTitle = true,
        public bool $showVersion = false,
        public bool $showUpdatedAt = true,
        public bool $showEffectiveDate = false,
        public bool $legalMode = false,
        public bool $allowTableOfContents = false,
    ) {
    }

    /** @return array<string, bool|string> */
    public function toArray(): array
    {
        return [
            'preferredTemplateKey' => $this->preferredTemplateKey,
            'contentWidth' => $this->contentWidth,
            'showTitle' => $this->showTitle,
            'showVersion' => $this->showVersion,
            'showUpdatedAt' => $this->showUpdatedAt,
            'showEffectiveDate' => $this->showEffectiveDate,
            'legalMode' => $this->legalMode,
            'allowTableOfContents' => $this->allowTableOfContents,
        ];
    }
}
