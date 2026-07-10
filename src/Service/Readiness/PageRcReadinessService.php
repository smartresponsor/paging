<?php

declare(strict_types=1);

namespace App\Paging\Service\Readiness;

use App\Paging\Command\PageUserUsabilityCommand;
use App\Paging\Controller\Api\PageAcceptanceController;
use App\Paging\Controller\Api\PageExportController;
use App\Paging\Controller\Api\PagePublicationController;
use App\Paging\Controller\Api\PageReadController;
use App\Paging\Controller\Api\PageRevisionController;
use App\Paging\Controller\Public\PageViewController;
use App\Paging\DTO\Readiness\PageRcChecklistItem;
use App\Paging\DTO\Readiness\PageRcReadinessReport;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageAcceptance;
use App\Paging\Entity\PageAttachmentReference;
use App\Paging\Entity\PageGrant;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;
use App\Paging\Form\PageForm;
use App\Paging\Form\PagePublicationForm;
use App\Paging\Form\PageRevisionForm;
use App\Paging\ServiceInterface\Acceptance\PageAcceptanceServiceInterface;
use App\Paging\ServiceInterface\Attachment\PageAttachmentReferenceServiceInterface;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;
use App\Paging\ServiceInterface\Contract\PageBridgePayloadFactoryInterface;
use App\Paging\ServiceInterface\Editor\PageEditorPayloadNormalizerInterface;
use App\Paging\ServiceInterface\Export\PageExportServiceInterface;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;
use App\Paging\ServiceInterface\Readiness\PageRcReadinessServiceInterface;
use App\Paging\ServiceInterface\Rendering\PageRenderServiceInterface;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;
use App\Paging\ServiceInterface\Usability\PageUserUsabilityServiceInterface;
use App\Paging\Voter\PageVoter;

/**
 * Builds a static RC readiness report for the Paging component.
 *
 * This service checks that the architectural surface needed for RC wiring is
 * present: entity-first model, service contracts, controllers, forms, local
 * grants, editor normalization, bridge output, and legal acceptance tracking.
 */
final class PageRcReadinessService implements PageRcReadinessServiceInterface
{
    public function buildReport(): PageRcReadinessReport
    {
        return new PageRcReadinessReport([
            $this->classes('entities', 'Entity-first Page model', [
                Page::class,
                PageRevision::class,
                PagePublication::class,
                PageAttachmentReference::class,
                PageGrant::class,
                PageAcceptance::class,
            ]),
            $this->classes('service_contracts', 'ServiceInterface business contracts', [
                PageDraftServiceInterface::class,
                PageRevisionServiceInterface::class,
                PagePublicationServiceInterface::class,
                PageRenderServiceInterface::class,
                PageExportServiceInterface::class,
                PageAttachmentReferenceServiceInterface::class,
                PageGrantServiceInterface::class,
                PageBridgePayloadFactoryInterface::class,
                PageEditorPayloadNormalizerInterface::class,
                PageAcceptanceServiceInterface::class,
            ]),
            $this->classes('controllers', 'Public/API controller surface', [
                PageViewController::class,
                PageReadController::class,
                PageExportController::class,
                PageRevisionController::class,
                PagePublicationController::class,
                PageAcceptanceController::class,
            ]),
            $this->classes('forms', 'Page form surface for Backofficing bridge', [
                PageForm::class,
                PageRevisionForm::class,
                PagePublicationForm::class,
            ]),
            $this->classes('security', 'Local grants and voter surface', [
                PageVoter::class,
                PageGrantServiceInterface::class,
            ]),
            $this->classes('user_usability', 'Host user/admin usability contract', [
                PageUserUsabilityServiceInterface::class,
                PageUserUsabilityCommand::class,
            ]),
        ]);
    }

    /** @param list<class-string> $classes */
    private function classes(string $code, string $label, array $classes): PageRcChecklistItem
    {
        $missing = [];
        foreach ($classes as $class) {
            if (!class_exists($class) && !interface_exists($class)) {
                $missing[] = $class;
            }
        }

        return new PageRcChecklistItem(
            $code,
            $label,
            [] === $missing,
            [] === $missing ? 'available' : 'missing: '.implode(', ', $missing),
        );
    }
}
