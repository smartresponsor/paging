<?php

declare(strict_types=1);

namespace App\Paging\Service\Finalization;

use App\Paging\Command\PageApiContractAuditCommand;
use App\Paging\Command\PageAuditReadinessCommand;
use App\Paging\Command\PageHostIntegrationCheckCommand;
use App\Paging\Command\PageInterfacingContractCommand;
use App\Paging\Command\PageOperationalChecklistCommand;
use App\Paging\Command\PageRcReadinessCommand;
use App\Paging\Command\PageSecurityContractCommand;
use App\Paging\Command\PageUserUsabilityCommand;
use App\Paging\Command\PageWorkflowAcceptanceCommand;
use App\Paging\DTO\Finalization\PageFinalStatusItemDTO;
use App\Paging\DTO\Finalization\PageFinalStatusReportDTO;
use App\Paging\Entity\PageAcceptanceEntity as PageAcceptance;
use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\FactoryInterface\Bridge\PageApiBridgePayloadFactoryInterface;
use App\Paging\PageBundle;
use App\Paging\ServiceInterface\Acceptance\PageAcceptanceServiceInterface;
use App\Paging\ServiceInterface\Api\PageApiContractServiceInterface;
use App\Paging\ServiceInterface\Export\PageExportServiceInterface;
use App\Paging\ServiceInterface\Finalization\PageFinalStatusServiceInterface;
use App\Paging\ServiceInterface\Operations\PageOperationChecklistServiceInterface;
use App\Paging\ServiceInterface\Readiness\PageRcReadinessServiceInterface;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;
use Doctrine\ORM\Mapping\Table;

/**
 * Produces the final RC closure status for the component-owned surface.
 *
 * The service deliberately checks only Paging responsibilities: Page naming,
 * page_ table prefix, page config prefix, bundle entry, service contracts,
 * runtime commands, smoke scripts, generated manifests, and user-usable host
 * contracts. Interfacing themes, Navigating placement, host security policy,
 * and Attachment storage remain outside this component boundary.
 */
final class PageFinalStatusService implements PageFinalStatusServiceInterface
{
    public function buildReport(): PageFinalStatusReportDTO
    {
        return new PageFinalStatusReportDTO([
            $this->classes('bundle_entry', 'Bundle and standalone component entrypoints exist', [PageBundle::class]),
            $this->classes('entity_surface', 'Page entity-first surface exists', [
                Page::class,
                PageRevision::class,
                PagePublication::class,
                PageAttachmentReference::class,
                PageGrant::class,
                PageAcceptance::class,
            ]),
            $this->tablePrefix('table_prefix', 'Doctrine table names use page/page_ prefix', [
                Page::class,
                PageRevision::class,
                PagePublication::class,
                PageAttachmentReference::class,
                PageGrant::class,
                PageAcceptance::class,
            ]),
            $this->classes('service_contracts', 'RC service contracts exist', [
                PageRcReadinessServiceInterface::class,
                PageOperationChecklistServiceInterface::class,
                PageApiContractServiceInterface::class,
                PageApiBridgePayloadFactoryInterface::class,
                PageExportServiceInterface::class,
                PageGrantServiceInterface::class,
                PageAcceptanceServiceInterface::class,
            ], true),
            $this->classes('rc_commands', 'RC audit and operations commands exist', [
                PageRcReadinessCommand::class,
                PageAuditReadinessCommand::class,
                PageHostIntegrationCheckCommand::class,
                PageApiContractAuditCommand::class,
                PageOperationalChecklistCommand::class,
                PageUserUsabilityCommand::class,
                PageInterfacingContractCommand::class,
                PageSecurityContractCommand::class,
                PageWorkflowAcceptanceCommand::class,
            ]),
            $this->paths('config_prefix', 'Config uses the page prefix', ['config/packages/page_config.yaml']),
            $this->paths('smoke_scripts', 'Windows-safe smoke scripts exist', [
                'tools/smoke/page-rc-smoke.ps1',
                'tools/smoke/page-host-integration-smoke.ps1',
                'tools/smoke/page-api-contract-smoke.ps1',
                'tools/smoke/page-operational-smoke.ps1',
                'tools/smoke/page-final-rc-smoke.ps1',
            ]),
            $this->paths('docs_and_manifests', 'Final RC docs and manifests exist', [
                'docs/rc/wave11-final-rc-validation.md',
                'docs/host/page-user-usability-integration.md',
                'docs/host/page-interfacing-e2e.md',
                'docs/host/page-security-access-integration.md',
                'docs/host/page-full-user-workflow-acceptance.md',
                'delivery/rc/generated/page-wave11-final-rc-validation-manifest.json',
            ]),
        ]);
    }

    /** @param list<class-string> $classes */
    private function classes(string $code, string $label, array $classes, bool $allowInterfaces = false): PageFinalStatusItemDTO
    {
        $missing = [];
        foreach ($classes as $class) {
            $available = class_exists($class) || ($allowInterfaces && interface_exists($class));
            if (!$available) {
                $missing[] = $class;
            }
        }

        return new PageFinalStatusItemDTO(
            $code,
            $label,
            [] === $missing,
            [] === $missing ? 'available' : 'missing: '.implode(', ', $missing),
        );
    }

    /** @param list<class-string> $classes */
    private function tablePrefix(string $code, string $label, array $classes): PageFinalStatusItemDTO
    {
        $invalid = [];
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                $invalid[] = $class.' is missing';
                continue;
            }

            $attributes = (new \ReflectionClass($class))->getAttributes(Table::class);
            if ([] === $attributes) {
                $invalid[] = $class.' has no Table attribute';
                continue;
            }

            $table = $attributes[0]->newInstance()->name;
            if (!is_string($table) || ('page' !== $table && !str_starts_with($table, 'page_'))) {
                $invalid[] = $class.' => '.$table;
            }
        }

        return new PageFinalStatusItemDTO(
            $code,
            $label,
            [] === $invalid,
            [] === $invalid ? 'page/page_ confirmed' : 'invalid: '.implode(', ', $invalid),
        );
    }

    /** @param list<string> $paths */
    private function paths(string $code, string $label, array $paths): PageFinalStatusItemDTO
    {
        $basePath = dirname(__DIR__, 3);
        $missing = [];
        foreach ($paths as $path) {
            if (!is_file($basePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path))) {
                $missing[] = $path;
            }
        }

        return new PageFinalStatusItemDTO(
            $code,
            $label,
            [] === $missing,
            [] === $missing ? 'available' : 'missing: '.implode(', ', $missing),
        );
    }
}
