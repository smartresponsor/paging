<?php

declare(strict_types=1);

namespace App\Paging\Service\Release;

use App\Paging\DTO\Release\PageReleaseStampItem;
use App\Paging\DTO\Release\PageReleaseStampReport;
use App\Paging\ServiceInterface\Completion\PageCompletionServiceInterface;
use App\Paging\ServiceInterface\Guard\PageCanonGuardServiceInterface;
use App\Paging\ServiceInterface\Handoff\PageHandoffSummaryServiceInterface;
use App\Paging\ServiceInterface\Release\PageReleaseStampServiceInterface;

final readonly class PageReleaseStampService implements PageReleaseStampServiceInterface
{
    public function __construct(
        private PageCompletionServiceInterface $completionService,
        private PageCanonGuardServiceInterface $canonGuardService,
        private PageHandoffSummaryServiceInterface $handoffSummaryService,
    ) {
    }

    public function buildReport(): PageReleaseStampReport
    {
        return new PageReleaseStampReport('Paging/Page RC1', [
            $this->completionStatus(),
            $this->canonStatus(),
            $this->handoffStatus(),
            $this->componentBoundary(),
            $this->nextLayerReadiness(),
        ]);
    }

    private function completionStatus(): PageReleaseStampItem
    {
        $report = $this->completionService->buildReport();

        return new PageReleaseStampItem(
            'completion_status',
            'Completion status',
            $report->passed(),
            $report->passed()
                ? sprintf('Completion report passed with %d check(s).', $report->passedCount())
                : sprintf('Completion report has %d failing check(s).', $report->failedCount()),
        );
    }

    private function canonStatus(): PageReleaseStampItem
    {
        $report = $this->canonGuardService->buildReport();

        return new PageReleaseStampItem(
            'canon_status',
            'Canon guard status',
            $report->passed(),
            $report->passed()
                ? sprintf('Canon guard passed with %d check(s).', $report->passedCount())
                : sprintf('Canon guard has %d failing check(s).', $report->failedCount()),
        );
    }

    private function handoffStatus(): PageReleaseStampItem
    {
        $report = $this->handoffSummaryService->buildReport();

        return new PageReleaseStampItem(
            'handoff_status',
            'Handoff status',
            $report->passed(),
            $report->passed()
                ? sprintf('Handoff report passed with %d check(s).', $report->passedCount())
                : sprintf('Handoff report has %d failing check(s).', $report->failedCount()),
        );
    }

    private function componentBoundary(): PageReleaseStampItem
    {
        return new PageReleaseStampItem(
            'component_boundary',
            'Component boundary',
            true,
            'Paging owns Page lifecycle, revisions, publications, acceptance, export, grants, and bridge payloads only.',
        );
    }

    private function nextLayerReadiness(): PageReleaseStampItem
    {
        return new PageReleaseStampItem(
            'next_layer_readiness',
            'Next layer readiness',
            true,
            'Backofficing may now consume Page forms/services; Interfacing may consume Page bridge payloads.',
        );
    }
}
