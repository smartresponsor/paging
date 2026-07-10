<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use App\Paging\Entity\Page;
use App\Paging\Entity\PageAcceptance;
use App\Paging\Entity\PageGrant;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/', routeName: 'page_admin')]
#[IsGranted('ROLE_ADMIN')]
final class PageAdminDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('page_admin_page_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Paging');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Pages', 'fa fa-file-lines', Page::class);
        yield MenuItem::linkToCrud('Revisions', 'fa fa-code-branch', PageRevision::class);
        yield MenuItem::linkToCrud('Publications', 'fa fa-bullhorn', PagePublication::class);
        yield MenuItem::linkToCrud('Grants', 'fa fa-key', PageGrant::class);
        yield MenuItem::linkToCrud('Acceptance history', 'fa fa-clipboard-check', PageAcceptance::class);
    }
}
