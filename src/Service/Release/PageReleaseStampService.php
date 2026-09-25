<?php

declare(strict_types=1);

namespace App\Paging\Service\Release;

use App\Paging\DTO\Release\PageReleaseStampItemDTO;
use App\Paging\DTO\Release\PageReleaseStampReportDTO;
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

    public function buildReport(): PageReleaseStampReportDTO
    {
        return new PageReleaseStampReportDTO('Paging/Page RC1', [
            $this->completionStatus(),
            $this->canonStatus(),
            $this->handoffStatus(),
            $this->componentBoundary(),
            $this->nextLayerReadiness(),
        ]);
    }

    private function completionStatus(): PageReleaseStampItemDTO
    {
        $report = $this->completionService->buildReport();

        return new PageReleaseStampItemDTO(
            'completion_status',
            'Completion status',
            $report->passed(),
            $report->passed()
                ? sprintf('Completion report passed with %d check(s).', $report->passedCount())
                : sprintf('Completion report has %d failing check(s).', $report->failedCount()),
        );
    }

    private function canonStatus(): PageReleaseStampItemDTO
    {
        $report = $this->canonGuardService->buildReport();

        return new PageReleaseStampItemDTO(
            'canon_status',
            'Canon guard status',
            $report->passed(),
            $report->passed()
                ? sprintf('Canon guard passed with %d check(s).', $report->passedCount())
                : sprintf('Canon guard has %d failing check(s).', $report->failedCount()),
        );
    }

    private function handoffStatus(): PageReleaseStampItemDTO
    {
        $report = $this->handoffSummaryService->buildReport();

        return new PageReleaseStampItemDTO(
            'handoff_status',
            'Handoff status',
            $report->passed(),
            $report->passed()
                ? sprintf('Handoff report passed with %d check(s).', $report->passedCount())
                : sprintf('Handoff report has %d failing check(s).', $report->failedCount()),
        );
    }

    private function componentBoundary(): PageReleaseStampItemDTO
    {
        return new PageReleaseStampItemDTO(
            'component_boundary',
            'Component boundary',
            true,
            'Paging owns Page lifecycle, revisions, publications, acceptance, export, grants, and bridge payloads only.',
        );
    }

    private function nextLayerReadiness(): PageReleaseStampItemDTO
    {
        return new PageReleaseStampItemDTO(
            'next_layer_readiness',
            'Next layer readiness',
            true,
            'Backofficing may now consume Page forms/services; Interfacing may consume Page bridge payloads.',
        );
    }
}
