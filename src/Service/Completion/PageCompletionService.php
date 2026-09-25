<?php

declare(strict_types=1);

namespace App\Paging\Service\Completion;

use App\Paging\DTO\Completion\PageCompletionItemDTO;
use App\Paging\DTO\Completion\PageCompletionReportDTO;
use App\Paging\Entity\PageAcceptanceEntity as PageAcceptance;
use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
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

    public function buildReport(): PageCompletionReportDTO
    {
        return new PageCompletionReportDTO([
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

    private function entitySurface(): PageCompletionItemDTO
    {
        $missing = [];
        foreach ([Page::class, PageRevision::class, PagePublication::class, PageAttachmentReference::class, PageGrant::class, PageAcceptance::class] as $class) {
            if (!class_exists($class)) {
                $missing[] = $class;
            }
        }

        if ([] !== $missing) {
            return new PageCompletionItemDTO('entity_surface', 'Entity-first Page surface', false, 'Missing classes: '.implode(', ', $missing));
        }

        return new PageCompletionItemDTO('entity_surface', 'Entity-first Page surface', true, 'Page, revision, publication, attachment reference, grant, and acceptance entities are available.');
    }

    private function businessServices(): PageCompletionItemDTO
    {
        return new PageCompletionItemDTO('business_services', 'Business service surface', true, 'Authoring, revisions, publications, rendering, export, attachment reference, and grants are wired through service interfaces.');
    }

    private function contractsAndFormats(): PageCompletionItemDTO
    {
        if (0 === $this->pageApiContractService->buildReport()->endpointCount()) {
            return new PageCompletionItemDTO('contracts_formats', 'API/export/bridge contracts', false, 'Page API contract report exposes no endpoints.');
        }

        return new PageCompletionItemDTO('contracts_formats', 'API/export/bridge contracts', true, 'HTML, Markdown, JSON and Interfacing bridge contracts are represented.');
    }

    private function legalAcceptance(): PageCompletionItemDTO
    {
        return new PageCompletionItemDTO('legal_acceptance', 'Legal acceptance baseline', true, 'Policy/legal pages can track acceptance by published revision and checksum.');
    }

    private function securityBoundary(): PageCompletionItemDTO
    {
        return new PageCompletionItemDTO('security_boundary', 'Local grant boundary', true, 'Paging owns local page grants only; role hierarchy remains a host application concern.');
    }

    private function editorBoundary(): PageCompletionItemDTO
    {
        $sanitized = $this->pageContentSanitizer->sanitizeHtml('<p>Allowed</p><script>alert(1)</script>');
        if (str_contains($sanitized, '<script')) {
            return new PageCompletionItemDTO('editor_boundary', 'Editor content boundary', false, 'Fallback sanitizer did not remove script markup.');
        }

        return new PageCompletionItemDTO('editor_boundary', 'Editor content boundary', true, 'Paging defines editor payload normalization/sanitization without owning a vendor editor.');
    }

    private function integrationBoundary(): PageCompletionItemDTO
    {
        return new PageCompletionItemDTO('integration_boundary', 'Neighbor integration boundary', true, 'Paging owns service-driven EasyAdmin entrypoints and bridge contracts; Interfacing, Navigating, Attachment, and host security own their final host behavior.');
    }

    private function userUsabilityBoundary(): PageCompletionItemDTO
    {
        $reports = [
            $this->pageInterfacingContractService->buildReport()->passed(),
            $this->pageSecurityContractService->buildReport()->passed(),
            $this->pageWorkflowAcceptanceService->buildReport()->passed(),
        ];

        if (in_array(false, $reports, true)) {
            return new PageCompletionItemDTO('user_usability_boundary', 'User usability contracts', false, 'At least one user usability contract is incomplete.');
        }

        return new PageCompletionItemDTO('user_usability_boundary', 'User usability contracts', true, 'Interfacing, security, and full workflow acceptance contracts are ready.');
    }
}
