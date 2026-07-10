<?php

declare(strict_types=1);

namespace App\Paging\Service\Workflow;

use App\Paging\DTO\Workflow\PageWorkflowAcceptanceReport;
use App\Paging\DTO\Workflow\PageWorkflowAcceptanceStep;
use App\Paging\ServiceInterface\Workflow\PageWorkflowAcceptanceServiceInterface;

final class PageWorkflowAcceptanceService implements PageWorkflowAcceptanceServiceInterface
{
    public function buildReport(): PageWorkflowAcceptanceReport
    {
        return new PageWorkflowAcceptanceReport([
            new PageWorkflowAcceptanceStep('create_page', 'Create page draft', 'Paging authoring', true, 'PageDraftServiceInterface'),
            new PageWorkflowAcceptanceStep('create_revision', 'Create page revision', 'Paging revisioning', true, 'PageRevisionServiceInterface'),
            new PageWorkflowAcceptanceStep('publish_revision', 'Publish revision', 'Paging publication', true, 'PagePublicationServiceInterface'),
            new PageWorkflowAcceptanceStep('view_public_page', 'View public page', 'Viewing/Public route', true, 'PageBridgeContractProviderInterface'),
            new PageWorkflowAcceptanceStep('interfacing_bridge', 'Render through Interfacing bridge', 'Viewing/Interfacing', true, 'PageInterfacingContractServiceInterface'),
            new PageWorkflowAcceptanceStep('navigating_entrypoints', 'Reach operator surface through navigation', 'Navigating', true, 'PageNavigationContractServiceInterface'),
            new PageWorkflowAcceptanceStep('security_access', 'Authorize through host security contract', 'Host security', true, 'PageSecurityContractServiceInterface'),
            new PageWorkflowAcceptanceStep('full_user_usability', 'Expose full host user usability report', 'Host application', true, 'PageUserUsabilityServiceInterface'),
        ]);
    }
}
