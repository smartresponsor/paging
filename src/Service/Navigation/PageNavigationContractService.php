<?php

declare(strict_types=1);

namespace App\Paging\Service\Navigation;

use App\Paging\DTO\Navigation\PageNavigationContractReport;
use App\Paging\DTO\Navigation\PageNavigationItem;
use App\Paging\ServiceInterface\Navigation\PageNavigationContractServiceInterface;

final class PageNavigationContractService implements PageNavigationContractServiceInterface
{
    public function buildReport(): PageNavigationContractReport
    {
        return new PageNavigationContractReport([
            $this->item('pages', 'Pages', '/admin/page?crudControllerFqcn=App%5CPaging%5CController%5CAdmin%5CPageCrudController', 'page_admin_page_index', 'page', 10, 'FileOutlined'),
            $this->item('revisions', 'Revisions', '/admin/page?crudControllerFqcn=App%5CPaging%5CController%5CAdmin%5CPageRevisionCrudController', 'page_admin_page_revision_index', 'revision', 20, 'BranchesOutlined'),
            $this->item('publications', 'Publications', '/admin/page?crudControllerFqcn=App%5CPaging%5CController%5CAdmin%5CPagePublicationCrudController', 'page_admin_page_publication_index', 'publication', 30, 'NotificationOutlined'),
            $this->item('grants', 'Page grants', '/admin/page?crudControllerFqcn=App%5CPaging%5CController%5CAdmin%5CPageGrantCrudController', 'page_admin_page_grant_index', 'grant', 40, 'KeyOutlined'),
            $this->item('acceptance', 'Acceptance history', '/admin/page?crudControllerFqcn=App%5CPaging%5CController%5CAdmin%5CPageAcceptanceCrudController', 'page_admin_page_acceptance_index', 'acceptance', 50, 'AuditOutlined'),
        ]);
    }

    private function item(string $key, string $label, string $path, string $routeName, string $resource, int $priority, string $icon): PageNavigationItem
    {
        return new PageNavigationItem(
            key: $key,
            label: $label,
            path: $path,
            routeName: $routeName,
            location: 'shell.left.bottom',
            operation: 'index',
            priority: 300 + $priority,
            icon: $icon,
            metadata: ['resource' => $resource, 'root_only' => true, 'crud_index_only' => true, 'operator_surface' => true]
        );
    }
}
