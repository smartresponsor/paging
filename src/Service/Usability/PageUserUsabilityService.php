<?php

declare(strict_types=1);

namespace App\Paging\Service\Usability;

use App\Paging\Controller\Admin\PageAcceptanceCrudController;
use App\Paging\Controller\Admin\PageAdminDashboardController;
use App\Paging\Controller\Admin\PageCrudController;
use App\Paging\Controller\Admin\PageGrantCrudController;
use App\Paging\Controller\Admin\PagePublicationCrudController;
use App\Paging\Controller\Admin\PageRevisionCrudController;
use App\Paging\Controller\Api\PageAcceptanceController;
use App\Paging\Controller\Api\PageAuthoringController;
use App\Paging\Controller\Api\PagePublicationController;
use App\Paging\Controller\Api\PageRevisionController;
use App\Paging\Controller\Public\PageViewController;
use App\Paging\DTO\Usability\PageUserUsabilityItem;
use App\Paging\DTO\Usability\PageUserUsabilityReport;
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
use App\Paging\ServiceInterface\Bridge\PageBridgeContractProviderInterface;
use App\Paging\ServiceInterface\Editor\PageEditorPayloadNormalizerInterface;
use App\Paging\ServiceInterface\Interfacing\PageInterfacingContractServiceInterface;
use App\Paging\ServiceInterface\Navigation\PageNavigationContractServiceInterface;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;
use App\Paging\ServiceInterface\Usability\PageUserUsabilityServiceInterface;
use App\Paging\Voter\PageVoter;

/**
 * Describes the component-owned surface needed by host UI layers.
 *
 * EasyAdmin, Backofficing, Viewing, Interfacing, Navigating, and host security
 * can use this report to verify that Paging exposes stable contracts without
 * duplicating Page lifecycle logic in UI controllers.
 */
final class PageUserUsabilityService implements PageUserUsabilityServiceInterface
{
    public function buildReport(): PageUserUsabilityReport
    {
        return new PageUserUsabilityReport([
            $this->classes('database_model', 'Paging', [
                Page::class,
                PageRevision::class,
                PagePublication::class,
                PageAttachmentReference::class,
                PageGrant::class,
                PageAcceptance::class,
            ]),
            $this->classes('backofficing_forms', 'Backofficing/EasyAdmin', [
                PageForm::class,
                PageRevisionForm::class,
                PagePublicationForm::class,
            ]),
            $this->classes('easyadmin_operator_surface', 'Backofficing/EasyAdmin', [
                PageAdminDashboardController::class,
                PageCrudController::class,
                PageRevisionCrudController::class,
                PagePublicationCrudController::class,
                PageGrantCrudController::class,
                PageAcceptanceCrudController::class,
            ]),
            $this->classes('service_driven_admin_actions', 'Backofficing/EasyAdmin', [
                PageDraftServiceInterface::class,
                PageRevisionServiceInterface::class,
                PagePublicationServiceInterface::class,
                PageAttachmentReferenceServiceInterface::class,
                PageGrantServiceInterface::class,
                PageEditorPayloadNormalizerInterface::class,
            ]),
            $this->classes('api_workflow', 'Host API', [
                PageAuthoringController::class,
                PageRevisionController::class,
                PagePublicationController::class,
                PageAcceptanceController::class,
            ]),
            $this->classes('public_viewing', 'Viewing/Interfacing', [
                PageViewController::class,
                PageBridgeContractProviderInterface::class,
                PageInterfacingContractServiceInterface::class,
            ]),
            $this->classes('host_security_bridge', 'Host security', [
                PageGrantServiceInterface::class,
                PageAcceptanceServiceInterface::class,
                PageVoter::class,
            ]),
            $this->classes('navigating_handoff', 'Navigating', [
                PageNavigationContractServiceInterface::class,
            ]),
        ]);
    }

    /** @param list<class-string> $classes */
    private function classes(string $code, string $ownerLayer, array $classes): PageUserUsabilityItem
    {
        $missing = [];
        foreach ($classes as $class) {
            if (!class_exists($class) && !interface_exists($class)) {
                $missing[] = $class;
            }
        }

        return new PageUserUsabilityItem($code, $ownerLayer, [] === $missing, [] === $missing ? 'available' : 'missing: '.implode(', ', $missing));
    }
}
