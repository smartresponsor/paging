<?php

declare(strict_types=1);

namespace App\Paging\ValueObject;

final readonly class PageChecksum
{
    public function __construct(private string $value)
    {
        $this->value = strtolower(trim($value));
        if (1 !== preg_match('/^[a-f0-9]{64}$/', $this->value)) {
            throw new \InvalidArgumentException('Page checksum must be a SHA-256 hex digest.');
        }
    }

    public static function fromContent(string ...$parts): self
    {
        return new self(hash('sha256', implode("\n---page-content-boundary---\n", $parts)));
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
