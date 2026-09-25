<?php

declare(strict_types=1);

namespace App\Paging\DTO\Handoff;

/**
 * One handoff item that describes a stable Paging/Page responsibility.
 */
final readonly class PageHandoffItemDTO
{
    public function __construct(
        public string $code,
        public string $status,
        public string $detail,
    ) {
    }

    /** @return array{code: string, status: string, detail: string} */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'status' => $this->status,
            'detail' => $this->detail,
        ];
    }
}
