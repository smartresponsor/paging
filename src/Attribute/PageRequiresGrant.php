<?php

declare(strict_types=1);

namespace App\Paging\Attribute;

use App\Paging\Enum\PageGrantType;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
final readonly class PageRequiresGrant
{
    public function __construct(public PageGrantType $grant)
    {
    }
}
