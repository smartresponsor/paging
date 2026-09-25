<?php

declare(strict_types=1);

namespace App\Paging\DTO\Workflow;

final readonly class PageWorkflowAcceptanceStepDTO
{
    public function __construct(
        public string $code,
        public string $label,
        public string $ownerLayer,
        public bool $componentReady,
        public string $evidence,
    ) {
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'label' => $this->label,
            'ownerLayer' => $this->ownerLayer,
            'componentReady' => $this->componentReady,
            'evidence' => $this->evidence,
        ];
    }
}
