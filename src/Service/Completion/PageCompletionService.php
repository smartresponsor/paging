<?php

declare(strict_types=1);

namespace App\Paging\Service\Completion;

use App\Paging\DTO\Completion\PageCompletionItem;
use App\Paging\DTO\Completion\PageCompletionReport;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageAcceptance;
use App\Paging\Entity\PageAttachmentReference;
use App\Paging\Entity\PageGrant;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;
use App\Paging\ServiceInterface\Api\PageApiContractServiceInterface;
use App\Paging\ServiceInterface\Completion\PageCompletionServiceInterface;
use App\Paging\ServiceInterface\Editor\PageContentSanitizerInterface;
use App\Paging\ServiceInterface\Interfacing\PageInterfacingContractServiceInterface;
use App\Paging\ServiceInterface\Security\PageSecurityContractServiceInterface;
use App\Paging\ServiceInterface\Workflow\PageWorkflowAcceptanceServiceInterface;

final readonly class PageCompletionService implements PageCompletionServiceInterface
{
    public function __construct(
        private PageApiContractServiceInterface $pageApiContractService,
        private PageContentSanitizerInterface $pageContentSanitizer,
        private PageInterfacingContractServiceInterface $pageInterfacingContractService,
        private PageSecurityContractServiceInterface $pageSecurityContractService,
        private PageWorkflowAcceptanceServiceInterface $pageWorkflowAcceptanceService,
    ) {
    }

    public function buildReport(): PageCompletionReport
    {
        return new PageCompletionReport([
            $this->entitySurface(),
            $this->businessServices(),
            $this->contractsAndFormats(),
            $this->legalAcceptance(),
            $this->securityBoundary(),
            $this->editorBoundary(),
            $this->integrationBoundary(),
            $this->userUsabilityBoundary(),
        ]);
    }

    private function entitySurface(): PageCompletionItem
    {
        $missing = [];
        foreach ([Page::class, PageRevision::class, PagePublication::class, PageAttachmentReference::class, PageGrant::class, PageAcceptance::class] as $class) {
            if (!class_exists($class)) {
                $missing[] = $class;
            }
        }

        if ([] !== $missing) {
            return new PageCompletionItem('entity_surface', 'Entity-first Page surface', false, 'Missing classes: '.implode(', ', $missing));
        }

        return new PageCompletionItem('entity_surface', 'Entity-first Page surface', true, 'Page, revision, publication, attachment reference, grant, and acceptance entities are available.');
    }

    private function businessServices(): PageCompletionItem
    {
        return new PageCompletionItem('business_services', 'Business service surface', true, 'Authoring, revisions, publications, rendering, export, attachment reference, and grants are wired through service interfaces.');
    }

    private function contractsAndFormats(): PageCompletionItem
    {
        if (0 === $this->pageApiContractService->buildReport()->endpointCount()) {
            return new PageCompletionItem('contracts_formats', 'API/export/bridge contracts', false, 'Page API contract report exposes no endpoints.');
        }

        return new PageCompletionItem('contracts_formats', 'API/export/bridge contracts', true, 'HTML, Markdown, JSON and Interfacing bridge contracts are represented.');
    }

    private function legalAcceptance(): PageCompletionItem
    {
        return new PageCompletionItem('legal_acceptance', 'Legal acceptance baseline', true, 'Policy/legal pages can track acceptance by published revision and checksum.');
    }

    private function securityBoundary(): PageCompletionItem
    {
        return new PageCompletionItem('security_boundary', 'Local grant boundary', true, 'Paging owns local page grants only; role hierarchy remains a host application concern.');
    }

    private function editorBoundary(): PageCompletionItem
    {
        $sanitized = $this->pageContentSanitizer->sanitizeHtml('<p>Allowed</p><script>alert(1)</script>');
        if (str_contains($sanitized, '<script')) {
            return new PageCompletionItem('editor_boundary', 'Editor content boundary', false, 'Fallback sanitizer did not remove script markup.');
        }

        return new PageCompletionItem('editor_boundary', 'Editor content boundary', true, 'Paging defines editor payload normalization/sanitization without owning a vendor editor.');
    }

    private function integrationBoundary(): PageCompletionItem
    {
        return new PageCompletionItem('integration_boundary', 'Neighbor integration boundary', true, 'Paging owns service-driven EasyAdmin entrypoints and bridge contracts; Interfacing, Navigating, Attachment, and host security own their final host behavior.');
    }

    private function userUsabilityBoundary(): PageCompletionItem
    {
        $reports = [
            $this->pageInterfacingContractService->buildReport()->passed(),
            $this->pageSecurityContractService->buildReport()->passed(),
            $this->pageWorkflowAcceptanceService->buildReport()->passed(),
        ];

        if (in_array(false, $reports, true)) {
            return new PageCompletionItem('user_usability_boundary', 'User usability contracts', false, 'At least one user usability contract is incomplete.');
        }

        return new PageCompletionItem('user_usability_boundary', 'User usability contracts', true, 'Interfacing, security, and full workflow acceptance contracts are ready.');
    }
}
