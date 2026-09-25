<?php

declare(strict_types=1);

namespace App\Paging\Service\Workflow;

use App\Paging\DTO\Workflow\PageWorkflowAcceptanceReportDTO;
use App\Paging\DTO\Workflow\PageWorkflowAcceptanceStepDTO;
use App\Paging\ServiceInterface\Workflow\PageWorkflowAcceptanceServiceInterface;

final class PageWorkflowAcceptanceService implements PageWorkflowAcceptanceServiceInterface
{
    public function buildReport(): PageWorkflowAcceptanceReportDTO
    {
        return new PageWorkflowAcceptanceReportDTO([
            new PageWorkflowAcceptanceStepDTO('create_page', 'Create page draft', 'Paging authoring', true, 'PageDraftServiceInterface'),
            new PageWorkflowAcceptanceStepDTO('create_revision', 'Create page revision', 'Paging revisioning', true, 'PageRevisionServiceInterface'),
            new PageWorkflowAcceptanceStepDTO('publish_revision', 'Publish revision', 'Paging publication', true, 'PagePublicationServiceInterface'),
            new PageWorkflowAcceptanceStepDTO('view_public_page', 'View public page', 'Viewing/Public route', true, 'PageBridgeContractProviderInterface'),
            new PageWorkflowAcceptanceStepDTO('interfacing_bridge', 'Render through Interfacing bridge', 'Viewing/Interfacing', true, 'PageInterfacingContractServiceInterface'),
            new PageWorkflowAcceptanceStepDTO('security_access', 'Authorize through host security contract', 'Host security', true, 'PageSecurityContractServiceInterface'),
            new PageWorkflowAcceptanceStepDTO('full_user_usability', 'Expose full host user usability report', 'Host application', true, 'PageUserUsabilityServiceInterface'),
        ]);
    }
}
