<?php

declare(strict_types=1);

namespace App\Paging\ValueObject;

use Symfony\Component\Uid\Uuid;

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
        $value = strtolower(trim($value, "/ \t\n\r\0\x0B"));
        if ('' === $value) {
            return self::randomUuidLike();
        }

        if (Uuid::isValid($value)) {
            return Uuid::fromString($value)->toRfc4122();
        }

        return self::uuidLikeFromSeed($value);
    }

    private static function uuidLikeFromSeed(string $seed): string
    {
        $hash = md5($seed);

        return sprintf(
            '%s-%s-4%s-8%s-%s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 3),
            substr($hash, 15, 3),
            substr($hash, 18, 12),
        );
    }

    private static function randomUuidLike(): string
    {
        return self::uuidLikeFromSeed(bin2hex(random_bytes(16)));
    }
}
