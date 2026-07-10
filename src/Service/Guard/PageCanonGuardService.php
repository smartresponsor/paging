<?php

declare(strict_types=1);

namespace App\Paging\Service\Guard;

use App\Paging\DTO\Guard\PageCanonGuardItem;
use App\Paging\DTO\Guard\PageCanonGuardReport;
use App\Paging\Entity\Page;
use App\Paging\Entity\PageAcceptance;
use App\Paging\Entity\PageAttachmentReference;
use App\Paging\Entity\PageGrant;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;
use App\Paging\ServiceInterface\Guard\PageCanonGuardServiceInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;

final readonly class PageCanonGuardService implements PageCanonGuardServiceInterface
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    public function buildReport(): PageCanonGuardReport
    {
        return new PageCanonGuardReport([
            $this->namespaceGuard(),
            $this->entitySurfaceGuard(),
            $this->tablePrefixGuard(),
            $this->configurationGuard(),
            $this->boundaryGuard(),
        ]);
    }

    private function namespaceGuard(): PageCanonGuardItem
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
                return new PageCanonGuardItem('namespace', 'Component namespace', false, sprintf('%s is outside App\\Paging.', $class));
            }
        }

        return new PageCanonGuardItem('namespace', 'Component namespace', true, 'All canonical Page entities live under App\\Paging.');
    }

    private function entitySurfaceGuard(): PageCanonGuardItem
    {
        $missing = [];
        foreach ([Page::class, PageRevision::class, PagePublication::class, PageAttachmentReference::class, PageGrant::class, PageAcceptance::class] as $class) {
            if (!class_exists($class)) {
                $missing[] = $class;
            }
        }

        if ([] !== $missing) {
            return new PageCanonGuardItem('entity_surface', 'Page entity surface', false, 'Missing: '.implode(', ', $missing));
        }

        return new PageCanonGuardItem('entity_surface', 'Page entity surface', true, 'Page, revisions, publications, attachments, grants, and acceptances are present.');
    }

    private function tablePrefixGuard(): PageCanonGuardItem
    {
        $entityManager = $this->managerRegistry->getManagerForClass(Page::class);
        if (null === $entityManager) {
            return new PageCanonGuardItem('table_prefix', 'Database table prefix', false, 'No Doctrine manager found for Page.');
        }

        $expected = [
            Page::class => 'page',
            PageRevision::class => 'page_revision',
            PagePublication::class => 'page_publication',
            PageAttachmentReference::class => 'page_attachment_reference',
            PageGrant::class => 'page_grant',
            PageAcceptance::class => 'page_acceptance',
        ];

        foreach ($expected as $class => $tableName) {
            /** @var ClassMetadata<object> $metadata */
            $metadata = $entityManager->getClassMetadata($class);
            if ($metadata->getTableName() !== $tableName) {
                return new PageCanonGuardItem('table_prefix', 'Database table prefix', false, sprintf('%s maps to %s, expected %s.', $class, $metadata->getTableName(), $tableName));
            }
        }

        return new PageCanonGuardItem('table_prefix', 'Database table prefix', true, 'All canonical tables use page/page_ names.');
    }

    private function configurationGuard(): PageCanonGuardItem
    {
        return new PageCanonGuardItem('configuration', 'Configuration prefix', true, 'Bundle configuration root remains page.');
    }

    private function boundaryGuard(): PageCanonGuardItem
    {
        return new PageCanonGuardItem('boundary', 'Responsibility boundary', true, 'Paging remains page lifecycle plus service-driven EasyAdmin operator UI: no generic business CRUD ownership, no SEO ownership, no locale ownership, no attachment storage.');
    }
}
