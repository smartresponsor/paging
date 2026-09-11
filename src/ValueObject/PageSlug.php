<?php

declare(strict_types=1);

namespace App\Paging\ValueObject;

final readonly class PageSlug
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = self::normalize($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromSource(string $value): self
    {
        return new self($value);
    }

    public static function random(): self
    {
        return new self(bin2hex(random_bytes(16)));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $value): string
    {
        $value = trim($value, "/ \t\n\r\0\x0B");
        $value = function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
        $value = preg_replace('/[^\\pL\\pN]+/u', '-', $value) ?? '';
        $value = trim($value, '-');

        return '' !== $value ? $value : bin2hex(random_bytes(16));
    }
}
