<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\Controller\Admin\PageAcceptanceCrudController;
use App\Paging\Controller\Admin\PageAdminDashboardController;
use App\Paging\Controller\Admin\PageCrudController;
use App\Paging\Controller\Admin\PageGrantCrudController;
use App\Paging\Controller\Admin\PagePublicationCrudController;
use App\Paging\Controller\Admin\PageRevisionCrudController;
use App\Paging\Controller\Public\PageHealthController;
use App\Paging\DependencyInjection\Configuration;
use App\Paging\DependencyInjection\PageExtension;
use App\Paging\DTO\Finalization\PageFinalStatusItemDTO;
use App\Paging\DTO\Finalization\PageFinalStatusReportDTO;
use App\Paging\DTO\Handoff\PageHandoffItemDTO;
use App\Paging\DTO\Handoff\PageHandoffReportDTO;
use App\Paging\DTO\Operations\PageOperationChecklistItemDTO;
use App\Paging\DTO\Operations\PageOperationChecklistReportDTO;
use App\Paging\DTO\Readiness\PageRcChecklistItemDTO;
use App\Paging\DTO\Readiness\PageRcReadinessReportDTO;
use App\Paging\DTO\Release\PageReleaseStampItemDTO;
use App\Paging\DTO\Release\PageReleaseStampReportDTO;
use App\Paging\DTO\Usability\PageUserUsabilityItemDTO;
use App\Paging\DTO\Usability\PageUserUsabilityReportDTO;
use App\Paging\DTO\Workflow\PageWorkflowAcceptanceReportDTO;
use App\Paging\DTO\Workflow\PageWorkflowAcceptanceStepDTO;
use App\Paging\Entity\PageAcceptanceEntity as PageAcceptance;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Form\PageForm;
use App\Paging\Form\PagePublicationForm;
use App\Paging\Form\PageRevisionForm;
use App\Paging\ResolverInterface\Security\PageSecuritySubjectResolverInterface;
use App\Paging\Service\Editor\PageContentSanitizer;
use App\Paging\ServiceInterface\Authoring\PageDraftServiceInterface;
use App\Paging\ServiceInterface\Publication\PagePublicationServiceInterface;
use App\Paging\ServiceInterface\Revision\PageRevisionServiceInterface;
use App\Paging\ServiceInterface\Runtime\PageRuntimeProbeServiceInterface;
use App\Paging\ServiceInterface\Security\PageGrantServiceInterface;
use App\Paging\Voter\PageVoter;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class PageFrameworkCoverageTest extends TestCase
{
    public function testReadOnlyAdminCrudSurfacesExposeConfiguredFieldsAndActions(): void
    {
        $controllers = [
            new PageAcceptanceCrudController(),
            new PageGrantCrudController(),
            new PagePublicationCrudController(),
        ];

        self::assertSame(PageAcceptance::class, PageAcceptanceCrudController::getEntityFqcn());
        self::assertSame(PageGrant::class, PageGrantCrudController::getEntityFqcn());
        self::assertSame(PagePublication::class, PagePublicationCrudController::getEntityFqcn());

        foreach ($controllers as $controller) {
            self::assertInstanceOf(Crud::class, $controller->configureCrud(Crud::new()));
            self::assertInstanceOf(Actions::class, $controller->configureActions(Actions::new()));
            self::assertNotEmpty(iterator_to_array($controller->configureFields(Crud::PAGE_INDEX)));
        }
    }

    public function testRevisionAndPageAdminConfigurationSurfacesAreExecutable(): void
    {
        $publicationService = $this->createStub(PagePublicationServiceInterface::class);
        $revisionController = new PageRevisionCrudController($publicationService);

        self::assertSame(PageRevision::class, PageRevisionCrudController::getEntityFqcn());
        self::assertInstanceOf(Crud::class, $revisionController->configureCrud(Crud::new()));
        self::assertInstanceOf(Actions::class, $revisionController->configureActions(Actions::new()));
        self::assertNotEmpty(iterator_to_array($revisionController->configureFields(Crud::PAGE_DETAIL)));

        $draftService = $this->createStub(PageDraftServiceInterface::class);
        $revisionService = $this->createStub(PageRevisionServiceInterface::class);
        $pageController = new PageCrudController($draftService, $revisionService);
        self::assertSame(Page::class, PageCrudController::getEntityFqcn());
        self::assertInstanceOf(Crud::class, $pageController->configureCrud(Crud::new()));
        self::assertInstanceOf(Actions::class, $pageController->configureActions(Actions::new()));
        self::assertNotEmpty(iterator_to_array($pageController->configureFields(Crud::PAGE_EDIT)));
        self::assertInstanceOf(Page::class, $pageController->createEntity(Page::class));

        $dashboard = new PageAdminDashboardController();
        self::assertSame('Paging', $dashboard->configureDashboard()->getAsDto()->getTitle());
        self::assertNotEmpty($dashboard->configureAssets()->getAsDto()->getCssAssets());
        self::assertCount(5, iterator_to_array($dashboard->configureMenuItems()));
    }

    public function testPageCrudPersistsAndUpdatesThroughBusinessServices(): void
    {
        $created = new Page('created', 'created', 'Created');
        $draftService = $this->createMock(PageDraftServiceInterface::class);
        $draftService->expects(self::once())->method('createPage')->willReturn($created);
        $draftService->expects(self::once())->method('updatePage')->willReturnArgument(0);

        $revisionService = $this->createMock(PageRevisionServiceInterface::class);
        $revisionService->expects(self::once())->method('createRevision');
        $controller = new PageCrudController($draftService, $revisionService);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $newFormPage = new Page('created', 'created', 'Created');
        $newFormPage->setDraftBodyHtml('   ');
        $controller->persistEntity($entityManager, $newFormPage);

        $existing = new Page('existing', 'existing', 'Existing');
        $existing->setDraftBodyHtml('<p>New body</p>');
        $controller->updateEntity($entityManager, $existing);
        self::addToAssertionCount(1);
    }

    public function testSymfonyFormTypesExposeFieldAndOptionContracts(): void
    {
        foreach ([new PageForm(), new PageRevisionForm(), new PagePublicationForm()] as $type) {
            $builder = $this->createStub(FormBuilderInterface::class);
            $builder->method('add')->willReturnSelf();
            $type->buildForm($builder, []);

            $resolver = new OptionsResolver();
            $type->configureOptions($resolver);
            self::assertArrayHasKey('data_class', $resolver->resolve());
        }
    }

    public function testDependencyInjectionConfigurationAndExtensionContracts(): void
    {
        $tree = (new Configuration())->getConfigTreeBuilder();
        self::assertSame('page', $tree->buildTree()->getName());

        $extension = new PageExtension();
        self::assertSame('page', $extension->getAlias());

        $withoutTwig = new ContainerBuilder();
        $extension->prepend($withoutTwig);

        $container = new ContainerBuilder();
        $container->registerExtension(new \Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension());
        $extension->prepend($container);
        $extension->load([], $container);
        self::assertSame('/page', $container->getParameter('page.public_route_prefix'));
        self::assertSame('/api/page', $container->getParameter('page.api_route_prefix'));
    }

    public function testSanitizerAndHealthControllerExposeSafeRuntimeBoundaries(): void
    {
        $sanitizer = new PageContentSanitizer();
        $html = $sanitizer->sanitizeHtml('<p onclick="x()">Hello <strong>World</strong></p><script>alert(1)</script>');
        self::assertSame('<p>Hello <strong>World</strong></p>', $html);
        self::assertSame('Hello World', $sanitizer->toPlainText('<h1>Hello</h1><p>World</p>'));

        $probe = $this->createStub(PageRuntimeProbeServiceInterface::class);
        $probe->method('snapshot')->willReturn(['component' => 'Paging']);
        $response = (new PageHealthController())($probe);
        self::assertSame(['component' => 'Paging'], json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testVoterUsesCanonicalGrantAndSubjectContracts(): void
    {
        $grantService = $this->createMock(PageGrantServiceInterface::class);
        $grantService->expects(self::once())->method('isGranted')->willReturn(true);
        $subjectResolver = $this->createStub(PageSecuritySubjectResolverInterface::class);
        $subjectResolver->method('userId')->willReturn('user-1');
        $subjectResolver->method('roles')->willReturn(['ROLE_USER']);
        $token = $this->createStub(TokenInterface::class);
        $voter = new PageVoter($grantService, $subjectResolver);

        self::assertGreaterThan(0, $voter->vote($token, new Page('voter', 'voter', 'Voter'), [PageVoter::VIEW]));
        self::assertSame(0, $voter->vote($token, 'not-a-page', [PageVoter::VIEW]));
        self::assertSame(0, $voter->vote($token, new Page('other', 'other', 'Other'), ['UNKNOWN']));
    }

    public function testReadinessReportObjectsExposeFailureAsWellAsSuccessBranches(): void
    {
        $final = new PageFinalStatusReportDTO([
            new PageFinalStatusItemDTO('ok', 'OK', true, 'ok'),
            new PageFinalStatusItemDTO('bad', 'Bad', false, 'bad'),
        ]);
        self::assertFalse($final->passed());
        self::assertSame(1, $final->passedCount());
        self::assertSame(1, $final->failedCount());
        self::assertFalse($final->toArray()['passed']);

        $handoff = new PageHandoffReportDTO([
            new PageHandoffItemDTO('ok', 'ready', 'ok'),
            new PageHandoffItemDTO('bad', 'blocked', 'bad'),
        ]);
        self::assertSame('attention_required', $handoff->status());
        self::assertFalse($handoff->passed());
        self::assertSame(1, $handoff->passedCount());
        self::assertSame(1, $handoff->failedCount());
        self::assertSame('attention_required', $handoff->toArray()['status']);

        $operations = new PageOperationChecklistReportDTO([
            new PageOperationChecklistItemDTO('ok', 'OK', true, 'ok'),
            new PageOperationChecklistItemDTO('bad', 'Bad', false, 'bad'),
        ]);
        self::assertFalse($operations->passed());
        self::assertSame(1, $operations->passedCount());
        self::assertSame(1, $operations->failedCount());

        $readiness = new PageRcReadinessReportDTO([
            new PageRcChecklistItemDTO('ok', 'OK', true, 'ok'),
            new PageRcChecklistItemDTO('bad', 'Bad', false, 'bad'),
        ]);
        self::assertFalse($readiness->passed());
        self::assertSame(1, $readiness->passedCount());
        self::assertSame(1, $readiness->failedCount());
        self::assertFalse($readiness->toArray()['passed']);

        $release = new PageReleaseStampReportDTO('RC', [
            new PageReleaseStampItemDTO('ok', 'OK', true, 'ok'),
            new PageReleaseStampItemDTO('bad', 'Bad', false, 'bad'),
        ]);
        self::assertFalse($release->passed());
        self::assertSame(1, $release->passedCount());
        self::assertSame(1, $release->failedCount());
        self::assertFalse($release->toArray()['passed']);

        $usability = new PageUserUsabilityReportDTO([
            new PageUserUsabilityItemDTO('ok', 'Host', true, 'ok'),
            new PageUserUsabilityItemDTO('bad', 'Host', false, 'bad'),
        ]);
        self::assertFalse($usability->componentReady());
        self::assertSame(1, $usability->readyCount());
        self::assertSame(1, $usability->pendingCount());
        self::assertSame(['Host'], $usability->ownerLayers());
        self::assertFalse($usability->toArray()['componentReady']);

        $workflow = new PageWorkflowAcceptanceReportDTO([
            new PageWorkflowAcceptanceStepDTO('bad', 'Bad', 'Host', false, 'bad'),
        ]);
        self::assertFalse($workflow->passed());
        self::assertSame(1, $workflow->stepCount());
        self::assertSame('incomplete', $workflow->toArray()['status']);
        self::assertFalse((new PageWorkflowAcceptanceReportDTO([]))->passed());
    }

    public function testDiagnosticCommandsExposeFailureBranches(): void
    {
        $canon = $this->createStub(\App\Paging\ServiceInterface\Guard\PageCanonGuardServiceInterface::class);
        $canon->method('buildReport')->willReturn(new \App\Paging\DTO\Guard\PageCanonGuardReportDTO([
            new \App\Paging\DTO\Guard\PageCanonGuardItemDTO('broken', 'Broken canon', false, 'missing'),
        ]));
        $tester = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageCanonGuardCommand($canon));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $tester->execute([]));

        $completion = $this->createStub(\App\Paging\ServiceInterface\Completion\PageCompletionServiceInterface::class);
        $completion->method('buildReport')->willReturn(new \App\Paging\DTO\Completion\PageCompletionReportDTO([
            new \App\Paging\DTO\Completion\PageCompletionItemDTO('broken', 'Broken completion', false, 'missing'),
        ]));
        $tester = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageCompletionStatusCommand($completion));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $tester->execute([]));

        $final = $this->createStub(\App\Paging\ServiceInterface\Finalization\PageFinalStatusServiceInterface::class);
        $final->method('buildReport')->willReturn(new PageFinalStatusReportDTO([
            new PageFinalStatusItemDTO('broken', 'Broken final status', false, 'missing'),
        ]));
        $tester = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageFinalRcStatusCommand($final));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $tester->execute([]));

        $release = $this->createStub(\App\Paging\ServiceInterface\Release\PageReleaseStampServiceInterface::class);
        $release->method('buildReport')->willReturn(new PageReleaseStampReportDTO('RC', [
            new PageReleaseStampItemDTO('broken', 'Broken release', false, 'missing'),
        ]));
        $tester = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageReleaseStampCommand($release));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $tester->execute([]));

        $operations = $this->createStub(\App\Paging\ServiceInterface\Operations\PageOperationChecklistServiceInterface::class);
        $operations->method('buildReport')->willReturn(new PageOperationChecklistReportDTO([
            new PageOperationChecklistItemDTO('broken', 'Broken operation', false, 'missing'),
        ]));
        $tester = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageOperationalChecklistCommand($operations));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $tester->execute([]));

        $readiness = $this->createStub(\App\Paging\ServiceInterface\Readiness\PageRcReadinessServiceInterface::class);
        $readiness->method('buildReport')->willReturn(new PageRcReadinessReportDTO([
            new PageRcChecklistItemDTO('broken', 'Broken readiness', false, 'missing'),
        ]));
        $tester = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageRcReadinessCommand($readiness));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $tester->execute([]));

        $usability = $this->createStub(\App\Paging\ServiceInterface\Usability\PageUserUsabilityServiceInterface::class);
        $usability->method('buildReport')->willReturn(new PageUserUsabilityReportDTO([
            new PageUserUsabilityItemDTO('broken', 'Host', false, 'missing'),
        ]));
        $human = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageUserUsabilityCommand($usability));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $human->execute([]));
        $json = new \Symfony\Component\Console\Tester\CommandTester(new \App\Paging\Command\PageUserUsabilityCommand($usability));
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $json->execute(['--json' => true]));
    }
}
