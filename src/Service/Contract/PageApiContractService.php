<?php

declare(strict_types=1);

namespace App\Paging\Service\Contract;

use App\Paging\DTO\Contract\PageApiContractReport;
use App\Paging\DTO\Contract\PageApiEndpointContract;
use App\Paging\ServiceInterface\Contract\PageApiContractServiceInterface;

/**
 * Defines the stable API/output contract for Page consumers.
 *
 * This is not an OpenAPI generator. It is a small, explicit contract registry
 * that keeps the first RC surface readable for host applications, bridge layers,
 * smoke tests, and documentation.
 */
final class PageApiContractService implements PageApiContractServiceInterface
{
    public function buildReport(): PageApiContractReport
    {
        return new PageApiContractReport([
            new PageApiEndpointContract(
                'page_public_index',
                'GET',
                '/page/',
                'Render the published Page index surface.',
                ['html'],
                true,
            ),
            new PageApiEndpointContract(
                'page_public_view',
                'GET',
                '/page/{slug}',
                'Render the published Page revision through the standalone Twig template.',
                ['html'],
                true,
            ),
            new PageApiEndpointContract(
                'page_api_read',
                'GET',
                '/api/page/{code}',
                'Return the published Page render view as JSON payload.',
                ['json'],
                true,
            ),
            new PageApiEndpointContract(
                'page_api_bridge',
                'GET',
                '/api/page/bridge/{code}',
                'Return the PageBridgePayload for Interfacing/host rendering bridges.',
                ['json'],
                true,
            ),
            new PageApiEndpointContract(
                'page_export_html',
                'GET',
                '/api/page/export/{code}?format=html',
                'Export the published Page revision as sanitized HTML.',
                ['html'],
                true,
            ),
            new PageApiEndpointContract(
                'page_export_json',
                'GET',
                '/api/page/export/{code}?format=json',
                'Export the published Page revision as a stable JSON document.',
                ['json'],
                true,
            ),
            new PageApiEndpointContract(
                'page_export_markdown',
                'GET',
                '/api/page/export/{code}?format=md',
                'Export the published Page revision as Markdown/source text when available.',
                ['markdown'],
                true,
            ),
            new PageApiEndpointContract(
                'page_revision_list',
                'GET',
                '/api/page/revision/{code}',
                'List Page revisions for authoring and audit surfaces.',
                ['json'],
            ),
            new PageApiEndpointContract(
                'page_revision_create',
                'POST',
                '/api/page/revision/{code}',
                'Create the next Page revision without mutating published history.',
                ['json'],
                false,
                true,
            ),
            new PageApiEndpointContract(
                'page_publication_list',
                'GET',
                '/api/page/publication/{code}',
                'List Page publication events for audit and legal traceability.',
                ['json'],
            ),
            new PageApiEndpointContract(
                'page_publication_create',
                'POST',
                '/api/page/publication/revision/{revisionNumber}',
                'Publish a concrete Page revision with effective-date metadata.',
                ['json'],
                false,
                true,
            ),
            new PageApiEndpointContract(
                'page_authoring_create',
                'POST',
                '/api/page/authoring/page',
                'Create a logical Page draft through the business authoring service.',
                ['json'],
                false,
                true,
            ),
            new PageApiEndpointContract(
                'page_authoring_update',
                'PATCH',
                '/api/page/authoring/page/{code}',
                'Update draft-level Page metadata without editing immutable revisions.',
                ['json'],
                false,
                true,
            ),
            new PageApiEndpointContract(
                'page_acceptance_create',
                'POST',
                '/api/page/acceptance/revision/{revisionNumber}',
                'Record user acceptance for a concrete Page revision/checksum.',
                ['json'],
                false,
                true,
            ),
            new PageApiEndpointContract(
                'page_acceptance_check',
                'GET',
                '/api/page/acceptance/subject/{subjectUserId}?code={code}&revisionNumber={revisionNumber}',
                'Check whether a subject accepted a concrete Page revision/checksum.',
                ['json'],
            ),
        ]);
    }
}
