<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Runtime;

interface PageRuntimeProbeServiceInterface
{
    /**
     * Returns a compact runtime snapshot for standalone and host-bundle diagnostics.
     *
     * @return array<string, mixed>
     */
    public function snapshot(): array;
}
