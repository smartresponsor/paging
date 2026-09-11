<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class PageEasyAdminSurfaceTest extends TestCase
{
    public function testNativeEasyAdminOperatorSurfaceFilesExist(): void
    {
        self::assertFileExists(__DIR__.'/../../src/Controller/Admin/PageAdminDashboardController.php');
        self::assertFileExists(__DIR__.'/../../src/Controller/Admin/PageCrudController.php');
        self::assertFileExists(__DIR__.'/../../src/Controller/Admin/PageRevisionCrudController.php');
        self::assertFileExists(__DIR__.'/../../src/Controller/Admin/PagePublicationCrudController.php');
        self::assertFileExists(__DIR__.'/../../src/Controller/Admin/PageGrantCrudController.php');
        self::assertFileExists(__DIR__.'/../../src/Controller/Admin/PageAcceptanceCrudController.php');
        self::assertFileExists(__DIR__.'/../../config/routes/easyadmin.yaml');
    }

    public function testDashboardIsRoleProtectedNativeEasyAdmin(): void
    {
        $dashboard = self::read('src/Controller/Admin/PageAdminDashboardController.php');

        self::assertStringContainsString('extends AbstractDashboardController', $dashboard);
        self::assertStringContainsString('#[AdminDashboard(', $dashboard);
        self::assertStringContainsString("routePath: ''", $dashboard);
        self::assertStringContainsString("routeName: 'page_admin'", $dashboard);
        self::assertStringContainsString("#[IsGranted('ROLE_ADMIN')]", $dashboard);
        self::assertStringContainsString("redirectToRoute('page_admin_page_index')", $dashboard);
    }

    public function testPageCrudIsServiceDrivenForMutations(): void
    {
        $controller = self::read('src/Controller/Admin/PageCrudController.php');

        self::assertStringContainsString('extends AbstractCrudController', $controller);
        self::assertStringContainsString("#[AdminRoute(path: '', name: 'page')]", $controller);
        self::assertStringContainsString("#[AdminRoute(path: '/index', name: 'index')]", $controller);
        self::assertStringContainsString("#[AdminRoute(path: '/new', name: 'new')]", $controller);
        self::assertStringContainsString("#[AdminRoute(path: '/edit/{entityId}', name: 'edit')]", $controller);
        self::assertStringContainsString("#[AdminRoute(path: '/detail/{entityId}', name: 'detail')]", $controller);
        self::assertStringContainsString("#[AdminRoute(path: '/delete/{entityId}', name: 'delete')]", $controller);
        self::assertStringContainsString('PageDraftServiceInterface', $controller);
        self::assertStringContainsString('PageRevisionServiceInterface', $controller);
        self::assertStringContainsString("TextEditorField::new('draftBodyHtml', 'Content')", $controller);
        self::assertStringContainsString("Action::new('revisions', 'Revisions'", $controller);
        self::assertStringContainsString('createPage(new PageCreateInput', $controller);
        self::assertStringContainsString('updatePage($entityInstance, new PageUpdateInput', $controller);
        self::assertStringNotContainsString('persist($entityInstance)', $controller);
    }

    public function testRevisionControllerPublishesThroughService(): void
    {
        $controller = self::read('src/Controller/Admin/PageRevisionCrudController.php');

        self::assertStringContainsString('PagePublicationServiceInterface', $controller);
        self::assertStringContainsString("Action::new('publishRevision', 'Publish')", $controller);
        self::assertStringContainsString('publishRevision($revision, new PagePublishInput())', $controller);
    }

    private static function read(string $relativePath): string
    {
        $contents = file_get_contents(dirname(__DIR__, 2).'/'.$relativePath);
        self::assertIsString($contents);

        return $contents;
    }
}
