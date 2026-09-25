<?php

declare(strict_types=1);

namespace App\Paging\DTO\Completion;

/**
 * One final completion/readiness item for the Paging/Page RC boundary.
 */
final readonly class PageCompletionItemDTO
{
    public function __construct(
        public string $code,
        public string $label,
        public bool $passed,
        public string $detail,
    ) {
    }

    /** @return array{code: string, label: string, passed: bool, detail: string} */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'label' => $this->label,
            'passed' => $this->passed,
            'detail' => $this->detail,
        ];
    }
}
