<?php

declare(strict_types=1);

namespace App\Paging\ValueObject;

final readonly class PageCode
{
    public function __construct(private string $value)
    {
        $this->value = trim($value);
        if ('' === $this->value || 1 !== preg_match('/^[a-z0-9][a-z0-9_\\-:.]{1,126}[a-z0-9]$/', $this->value)) {
            throw new \InvalidArgumentException('Page code must be a stable lowercase business identifier.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
