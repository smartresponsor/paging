<?php

declare(strict_types=1);

namespace App\Paging\ValueObject;

final readonly class PageRevisionNumber
{
    public function __construct(private int $value)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Page revision number must be greater than zero.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
