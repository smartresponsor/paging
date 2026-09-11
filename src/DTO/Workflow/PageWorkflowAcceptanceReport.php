<?php

declare(strict_types=1);

namespace App\Paging\DTO\Workflow;

final readonly class PageWorkflowAcceptanceReport
{
    public function __construct(public array $steps)
    {
    }

    public function passed(): bool
    {
        foreach ($this->steps as $step) {
            if (!$step->componentReady) {
                return false;
            }
        }

        return [] !== $this->steps;
    }

    public function stepCount(): int
    {
        return count($this->steps);
    }

    public function toArray(): array
    {
        return [
            'component' => 'paging',
            'scenario' => 'create_page_create_revision_publish_view_bridge_security',
            'status' => $this->passed() ? 'ready' : 'incomplete',
            'stepCount' => $this->stepCount(),
            'steps' => array_map(static fn (PageWorkflowAcceptanceStep $step): array => $step->toArray(), $this->steps),
        ];
    }
}
