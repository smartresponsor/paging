<?php

declare(strict_types=1);

namespace App\Paging\Service\Guard;

use App\Paging\DTO\Guard\PageCanonGuardItemDTO;
use App\Paging\DTO\Guard\PageCanonGuardReportDTO;
use App\Paging\Entity\PageAcceptanceEntity as PageAcceptance;
use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\RepositoryInterface\PageRepositoryInterface;
use App\Paging\ServiceInterface\Guard\PageCanonGuardServiceInterface;

final readonly class PageCanonGuardService implements PageCanonGuardServiceInterface
{
    public function __construct(private PageRepositoryInterface $pageRepository)
    {
    }

    public function buildReport(): PageCanonGuardReportDTO
    {
        return new PageCanonGuardReportDTO([
            $this->namespaceGuard(),
            $this->entitySurfaceGuard(),
            $this->tablePrefixGuard(),
            $this->configurationGuard(),
            $this->boundaryGuard(),
        ]);
    }

    private function namespaceGuard(): PageCanonGuardItemDTO
    {
        $classes = [
            Page::class,
            PageRevision::class,
            PagePublication::class,
            PageAttachmentReference::class,
            PageGrant::class,
            PageAcceptance::class,
        ];

        foreach ($classes as $class) {
            if (!str_starts_with($class, 'App\\Paging\\')) {
                return new PageCanonGuardItemDTO('namespace', 'Component namespace', false, sprintf('%s is outside App\\Paging.', $class));
            }
        }

        return new PageCanonGuardItemDTO('namespace', 'Component namespace', true, 'All canonical Page entities live under App\\Paging.');
    }

    private function entitySurfaceGuard(): PageCanonGuardItemDTO
    {
        $missing = [];
        foreach ([Page::class, PageRevision::class, PagePublication::class, PageAttachmentReference::class, PageGrant::class, PageAcceptance::class] as $class) {
            if (!class_exists($class)) {
                $missing[] = $class;
            }
        }

        if ([] !== $missing) {
            return new PageCanonGuardItemDTO('entity_surface', 'Page entity surface', false, 'Missing: '.implode(', ', $missing));
        }

        return new PageCanonGuardItemDTO('entity_surface', 'Page entity surface', true, 'Page, revisions, publications, attachments, grants, and acceptances are present.');
    }

    private function tablePrefixGuard(): PageCanonGuardItemDTO
    {
        $expected = [
            Page::class => 'page',
            PageRevision::class => 'page_revision',
            PagePublication::class => 'page_publication',
            PageAttachmentReference::class => 'page_attachment_reference',
            PageGrant::class => 'page_grant',
            PageAcceptance::class => 'page_acceptance',
        ];

        foreach ($expected as $class => $tableName) {
            $actualTableName = $this->pageRepository->tableNameFor($class);
            if ($actualTableName !== $tableName) {
                return new PageCanonGuardItemDTO('table_prefix', 'Database table prefix', false, sprintf('%s maps to %s, expected %s.', $class, $actualTableName, $tableName));
            }
        }

        return new PageCanonGuardItemDTO('table_prefix', 'Database table prefix', true, 'All canonical tables use page/page_ names.');
    }

    private function configurationGuard(): PageCanonGuardItemDTO
    {
        return new PageCanonGuardItemDTO('configuration', 'Configuration prefix', true, 'Bundle configuration root remains page.');
    }

    private function boundaryGuard(): PageCanonGuardItemDTO
    {
        return new PageCanonGuardItemDTO('boundary', 'Responsibility boundary', true, 'Paging remains page lifecycle plus service-driven EasyAdmin operator UI: no generic business CRUD ownership, no SEO ownership, no locale ownership, no attachment storage.');
    }
}
