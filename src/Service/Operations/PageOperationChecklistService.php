<?php

declare(strict_types=1);

namespace App\Paging\Service\Operations;

use App\Paging\Command\PageApiContractAuditCommand;
use App\Paging\Command\PageAuditReadinessCommand;
use App\Paging\Command\PageDebugContainerCommand;
use App\Paging\Command\PageHostIntegrationCheckCommand;
use App\Paging\Command\PageRcReadinessCommand;
use App\Paging\Command\PageSeedDemoCommand;
use App\Paging\DTO\Operations\PageOperationChecklistItem;
use App\Paging\DTO\Operations\PageOperationChecklistReport;
use App\Paging\PageBundle;
use App\Paging\ServiceInterface\Operations\PageOperationChecklistServiceInterface;

/**
 * Builds a final, static post-RC operations checklist.
 *
 * The checklist intentionally validates component-owned surfaces only: runtime
 * probes, RC commands, API contract commands, smoke scripts, docs, bundle entry,
 * and demo seeding. Host role hierarchy, EasyAdmin screens, and UI themes remain
 * outside the Paging component boundary.
 */
final class PageOperationChecklistService implements PageOperationChecklistServiceInterface
{
    public function buildReport(): PageOperationChecklistReport
    {
        return new PageOperationChecklistReport([
            $this->classes('bundle_entry', 'Bundle entrypoint exists', [PageBundle::class]),
            $this->classes('console_commands', 'Standalone and host console checks exist', [
                PageDebugContainerCommand::class,
                PageSeedDemoCommand::class,
                PageAuditReadinessCommand::class,
                PageRcReadinessCommand::class,
                PageHostIntegrationCheckCommand::class,
                PageApiContractAuditCommand::class,
            ]),
            $this->paths('smoke_scripts', 'Windows PowerShell smoke scripts exist', [
                'tools/smoke/page-rc-smoke.ps1',
                'tools/smoke/page-host-integration-smoke.ps1',
                'tools/smoke/page-api-contract-smoke.ps1',
                'tools/smoke/page-operational-smoke.ps1',
            ]),
            $this->paths('handoff_docs', 'Host/API/RC handoff docs exist', [
                'docs/host/page-host-integration.md',
                'docs/host/page-backofficing-bridge.md',
                'docs/host/page-interfacing-bridge.md',
                'docs/api/page-api-contract.md',
                'docs/api/page-response-shapes.md',
                'docs/rc/wave10-operational-handoff.md',
            ]),
            $this->paths('delivery_manifests', 'Generated delivery manifests exist', [
                'delivery/rc/generated/page-wave7-rc-closure-manifest.json',
                'delivery/rc/generated/page-wave8-host-integration-hardening-manifest.json',
                'delivery/rc/generated/page-wave9-api-contract-stabilization-manifest.json',
                'delivery/rc/generated/page-wave10-operational-handoff-manifest.json',
            ]),
        ]);
    }

    /** @param list<class-string> $classes */
    private function classes(string $code, string $label, array $classes): PageOperationChecklistItem
    {
        $missing = [];
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                $missing[] = $class;
            }
        }

        return new PageOperationChecklistItem(
            $code,
            $label,
            [] === $missing,
            [] === $missing ? 'available' : 'missing: '.implode(', ', $missing),
        );
    }

    /** @param list<string> $paths */
    private function paths(string $code, string $label, array $paths): PageOperationChecklistItem
    {
        $basePath = dirname(__DIR__, 3);
        $missing = [];
        foreach ($paths as $path) {
            if (!is_file($basePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path))) {
                $missing[] = $path;
            }
        }

        return new PageOperationChecklistItem(
            $code,
            $label,
            [] === $missing,
            [] === $missing ? 'available' : 'missing: '.implode(', ', $missing),
        );
    }
}
