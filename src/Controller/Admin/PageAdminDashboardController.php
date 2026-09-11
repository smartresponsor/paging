<?php

declare(strict_types=1);

namespace App\Paging\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '', routeName: 'page_admin')]
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

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('/bundles/page/easyadmin/page-admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa fa-file-lines');
        yield MenuItem::linkTo(PageRevisionCrudController::class, 'Revisions', 'fa fa-code-branch');
        yield MenuItem::linkTo(PagePublicationCrudController::class, 'Publications', 'fa fa-bullhorn');
        yield MenuItem::linkTo(PageGrantCrudController::class, 'Grants', 'fa fa-key');
        yield MenuItem::linkTo(PageAcceptanceCrudController::class, 'Acceptance history', 'fa fa-clipboard-check');
    }
}
