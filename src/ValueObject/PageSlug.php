<?php

declare(strict_types=1);

namespace App\Paging\ValueObject;

final readonly class PageSlug
{
    public function __construct(private string $value)
    {
        $this->value = trim($value, '/ ');
        if ('' === $this->value || 1 !== preg_match('/^[a-z0-9][a-z0-9\/-]{0,253}[a-z0-9]$/', $this->value)) {
            throw new \InvalidArgumentException('Page slug must be a lowercase route-safe slug.');
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
