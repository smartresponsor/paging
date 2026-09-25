<?php

declare(strict_types=1);

namespace App\Paging\Tests\Integration;

use App\Paging\Controller\Api\PageAcceptanceController;
use App\Paging\Controller\Api\PageAuthoringController;
use App\Paging\Controller\Api\PageExportController;
use App\Paging\Controller\Api\PagePublicationController;
use App\Paging\Controller\Api\PageReadController;
use App\Paging\Controller\Api\PageRevisionController;
use App\Paging\Controller\Public\PageViewController;
use App\Paging\DataFixtures\PageDemoFixtures;
use App\Paging\DTO\Authoring\PageCreateInputDTO;
use App\Paging\DTO\Publication\PagePublishInputDTO;
use App\Paging\DTO\Revision\PageRevisionCreateInputDTO;
use App\Paging\Enum\PageKind;
use App\Paging\Factory\Bridge\PageApiBridgePayloadFactory;
use App\Paging\Factory\Bridge\PageBridgePayloadFactory;
use App\Paging\Factory\Http\PageHttpPayloadFactory;
use App\Paging\Provider\Bridge\PageBridgeContractProvider;
use App\Paging\Repository\PageAcceptanceRepository;
use App\Paging\Repository\PageAttachmentReferenceRepository;
use App\Paging\Repository\PageGrantRepository;
use App\Paging\Repository\PagePublicationRepository;
use App\Paging\Repository\PageRepository;
use App\Paging\Repository\PageRevisionRepository;
use App\Paging\Service\Acceptance\PageAcceptanceService;
use App\Paging\Service\Authoring\PageDraftService;
use App\Paging\Service\Export\PageExportService;
use App\Paging\Service\Publication\PagePublicationService;
use App\Paging\Service\Rendering\PageRenderService;
use App\Paging\Service\Revision\PageRevisionService;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageApplicationFlowTest extends TestCase
{
    private EntityManager $entityManager;
    private PageRepository $pageRepository;
    private PageRevisionRepository $pageRevisionRepository;
    private PagePublicationRepository $pagePublicationRepository;
    private PageAcceptanceRepository $pageAcceptanceRepository;

    protected function setUp(): void
    {
        $projectDir = dirname(__DIR__, 2);
        $config = ORMSetup::createAttributeMetadataConfig([$projectDir.'/src/Entity'], true);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());
        $config->enableNativeLazyObjects(true);

        $this->entityManager = new EntityManager(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]), $config);
        (new SchemaTool($this->entityManager))->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        (new PageDemoFixtures())->load($this->entityManager);

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);
        $this->pageRepository = new PageRepository($registry);
        $this->pageRevisionRepository = new PageRevisionRepository($registry);
        $this->pagePublicationRepository = new PagePublicationRepository($registry);
        $this->pageAcceptanceRepository = new PageAcceptanceRepository($registry);
    }

    public function testAllPagingRepositoriesResolveAgainstDoctrineMetadata(): void
    {
        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);

        foreach ([
            new PageAcceptanceRepository($registry),
            new PageAttachmentReferenceRepository($registry),
            new PageGrantRepository($registry),
            new PagePublicationRepository($registry),
            new PageRepository($registry),
            new PageRevisionRepository($registry),
        ] as $repository) {
            $repository->findAll();
            self::addToAssertionCount(1);
        }
    }

    public function testPublishedRepositoryAndPublicViewUsePublishedContent(): void
    {
        $published = $this->pageRepository->findPublishedOrdered();
        self::assertCount(4, $published);

        $controller = new PageViewController($this->pageRepository, new PageRenderService());
        self::assertSame('/page/{slug}', $controller->index()['data']['resourcePath']);

        $payload = $controller('home');
        self::assertSame('home', $payload['data']['page']->getCode());
        self::assertSame(2, $payload['data']['view']->version);
    }

    public function testPublicViewRejectsMissingPage(): void
    {
        $controller = new PageViewController($this->pageRepository, new PageRenderService());

        $this->expectException(NotFoundHttpException::class);
        $controller('missing-page');
    }

    public function testAuthoringRevisionAndPublicationServicesPersistLifecycle(): void
    {
        $draftService = new PageDraftService($this->pageRepository);
        $revisionService = new PageRevisionService($this->pageRevisionRepository);
        $publicationService = new PagePublicationService($this->pagePublicationRepository);

        $page = $draftService->createPage(new PageCreateInputDTO('faq', 'faq', 'FAQ', PageKind::Help, 'owner-9'));
        $revision = $revisionService->createRevision($page, new PageRevisionCreateInputDTO(
            'FAQ',
            '<h1>FAQ</h1><p>Answer</p>',
            bodyMarkdown: "# FAQ\n\nAnswer",
            changeNote: 'Initial FAQ',
            createdByUserId: 'owner-9',
        ));
        $publication = $publicationService->publishRevision($revision, new PagePublishInputDTO(
            new \DateTimeImmutable('2026-09-16T12:00:00-05:00'),
            null,
            'owner-9',
        ));

        self::assertSame(1, $revision->getRevisionNumber());
        self::assertSame('FAQAnswer', $revision->getBodyText());
        self::assertTrue($revision->isLocked());
        self::assertSame($revision, $page->getPublishedRevision());
        self::assertSame($page, $publication->getPage());
        self::assertSame('owner-9', $publication->getPublishedByUserId());
    }

    public function testExportControllerServesSupportedFormatsAndRejectsUnknownFormat(): void
    {
        $controller = new PageExportController(
            $this->pageRepository,
            new PageExportService(new PageRenderService()),
        );

        foreach (['html', 'json', 'md'] as $format) {
            $response = $controller->export('home', Request::create('/api/page/export/home', 'GET', ['format' => $format]));
            self::assertSame(Response::HTTP_OK, $response->getStatusCode());
            self::assertNotSame('', $response->getContent());
            self::assertNotNull($response->headers->get('ETag'));
        }

        $this->expectException(NotFoundHttpException::class);
        $controller->export('home', Request::create('/api/page/export/home', 'GET', ['format' => 'pdf']));
    }

    public function testExportControllerRejectsMissingPublishedPage(): void
    {
        $controller = new PageExportController(
            $this->pageRepository,
            new PageExportService(new PageRenderService()),
        );

        $this->expectException(NotFoundHttpException::class);
        $controller->export('missing', Request::create('/api/page/export/missing'));
    }

    public function testBridgeProviderResolvesObjectingSlugAndLegalHints(): void
    {
        $provider = new PageBridgeContractProvider(
            $this->pageRepository,
            new PageBridgePayloadFactory(new PageRenderService()),
        );

        self::assertSame('home', $provider->byCode('home')->code);

        $legal = $provider->bySlug('shipping-returns');
        self::assertSame('shipping-returns', $legal->slug);
        self::assertTrue($legal->renderHints->legalMode);
        self::assertNotNull($legal->legalNotice);
    }

    public function testBridgeProviderRejectsMissingSlug(): void
    {
        $provider = new PageBridgeContractProvider(
            $this->pageRepository,
            new PageBridgePayloadFactory(new PageRenderService()),
        );

        $this->expectException(\RuntimeException::class);
        $provider->bySlug('missing-page');
    }

    public function testBridgeProviderRejectsMissingCode(): void
    {
        $provider = new PageBridgeContractProvider(
            $this->pageRepository,
            new PageBridgePayloadFactory(new PageRenderService()),
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Page with code "missing-page" was not found for bridge output.');
        $provider->byCode('missing-page');
    }

    public function testApiControllersSupportCompleteAuthoringToAcceptanceWorkflow(): void
    {
        $httpFactory = new PageHttpPayloadFactory();
        $draftService = new PageDraftService($this->pageRepository);
        $revisionService = new PageRevisionService($this->pageRevisionRepository);
        $publicationService = new PagePublicationService($this->pagePublicationRepository);

        $authoring = new PageAuthoringController($this->pageRepository, $draftService, $httpFactory);
        $created = $this->responseArray($authoring->create(Request::create(
            '/api/page/authoring/page',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'code' => 'api-help',
                'slug' => 'API Help',
                'title' => 'API Help',
                'kind' => 'help',
                'ownerUserId' => 'owner-api',
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame('api-help', $created['page']['code']);
        self::assertSame('api-help', $created['page']['slug']);

        $updated = $this->responseArray($authoring->update('api-help', Request::create(
            '/api/page/authoring/page/api-help',
            'PATCH',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'title' => 'API Help Updated',
                'slug' => 'API Help Updated',
                'ownerUserId' => null,
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame('API Help Updated', $updated['page']['title']);
        self::assertNull($updated['page']['ownerUserId']);

        $revisionController = new PageRevisionController($this->pageRepository, $revisionService, $httpFactory);
        $revision = $this->responseArray($revisionController->create('api-help', Request::create(
            '/api/page/revision/api-help',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'title' => 'API Help v1',
                'bodyHtml' => '<h1>API Help</h1><p>Body</p>',
                'bodyMarkdown' => '# API Help',
                'bodyJson' => ['blocks' => [['type' => 'text']]],
                'changeNote' => 'first',
                'createdByUserId' => 'editor-api',
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame(1, $revision['revision']['revisionNumber']);

        $listedRevisions = $this->responseArray($revisionController->list('api-help'));
        self::assertCount(1, $listedRevisions['revisions']);

        $publicationController = new PagePublicationController($this->pageRepository, $publicationService, $httpFactory);
        $publication = $this->responseArray($publicationController->publish(1, Request::create(
            '/api/page/publication/revision/1',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'code' => 'api-help',
                'effectiveFrom' => '2026-09-17T00:00:00-05:00',
                'expiresAt' => '2027-09-17T00:00:00-05:00',
                'publishedByUserId' => 'publisher-api',
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame('published', $publication['page']['status']);
        self::assertSame('2026-09-17T00:00:00-05:00', $publication['publication']['effectiveFrom']);

        // A new HTTP request gets a fresh managed Page instance; emulate that lifecycle here.
        $this->entityManager->clear();
        $listedPublications = $this->responseArray($publicationController->list('api-help'));
        self::assertCount(1, $listedPublications['publications']);

        $bridgeFactory = new PageApiBridgePayloadFactory(new PageRenderService());
        $readController = new PageReadController($this->pageRepository, new PageRenderService(), $bridgeFactory, $httpFactory);
        $read = $this->responseArray($readController->read('api-help'));
        self::assertSame(1, $read['render']['version']);
        $bridge = $this->responseArray($readController->bridge('api-help'));
        self::assertSame('api-help', $bridge['bridge']['code']);

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);
        $acceptanceService = new PageAcceptanceService($this->pageAcceptanceRepository);
        $acceptanceController = new PageAcceptanceController($this->pageRepository, $acceptanceService);

        $missingSubject = $acceptanceController->accept(1, Request::create(
            '/api/page/acceptance/revision/1',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['code' => 'api-help'], JSON_THROW_ON_ERROR),
        ));
        self::assertSame(422, $missingSubject->getStatusCode());

        $accepted = $this->responseArray($acceptanceController->accept(1, Request::create(
            '/api/page/acceptance/revision/1',
            'POST',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'REMOTE_ADDR' => '203.0.113.10',
                'HTTP_USER_AGENT' => 'PagingTest/1.0',
            ],
            content: json_encode([
                'code' => 'api-help',
                'subjectUserId' => 'subject-api',
                'acceptanceContext' => ['surface' => 'integration'],
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame('subject-api', $accepted['acceptance']['subjectUserId']);
        self::assertNotNull($accepted['acceptance']['ipHash']);

        $checked = $this->responseArray($acceptanceController->check('subject-api', Request::create(
            '/api/page/acceptance/revision/1/subject/subject-api?code=api-help&revisionNumber=1',
            'GET',
        )));
        self::assertTrue($checked['accepted']);
    }

    public function testApiControllersRejectMissingPagesCodesAndRevisions(): void
    {
        $httpFactory = new PageHttpPayloadFactory();
        $read = new PageReadController(
            $this->pageRepository,
            new PageRenderService(),
            new PageApiBridgePayloadFactory(new PageRenderService()),
            $httpFactory,
        );

        try {
            $read->read('missing');
            self::fail('Missing published page should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertStringContainsString('Published page', $exception->getMessage());
        }

        $publication = new PagePublicationController(
            $this->pageRepository,
            new PagePublicationService($this->pagePublicationRepository),
            $httpFactory,
        );
        try {
            $publication->publish(999, Request::create('/api/page/publication/revision/999', 'POST'));
            self::fail('Missing page code should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page code is required.', $exception->getMessage());
        }

        try {
            $publication->publish(999, Request::create('/api/page/publication/revision/999?code=home', 'POST'));
            self::fail('Missing revision should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertStringContainsString('revision 999', $exception->getMessage());
        }

        $authoring = new PageAuthoringController($this->pageRepository, new PageDraftService($this->pageRepository), $httpFactory);
        try {
            $authoring->update('missing', Request::create('/api/page/authoring/page/missing', 'PATCH', content: '{}'));
            self::fail('Missing draft page should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertStringContainsString('was not found', $exception->getMessage());
        }
    }

    public function testApiBoundaryFallbacksAndMissingResourcesAreExplicit(): void
    {
        $httpFactory = new PageHttpPayloadFactory();
        $revisionController = new PageRevisionController(
            $this->pageRepository,
            new PageRevisionService($this->pageRevisionRepository),
            $httpFactory,
        );

        $scalarRevision = $this->responseArray($revisionController->create('home', Request::create(
            '/api/page/revision/home',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '"not-an-object"',
        )));
        self::assertSame('Marketplace home', $scalarRevision['revision']['title']);

        try {
            $revisionController->list('missing-page');
            self::fail('Missing revision page should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertStringContainsString('Page "missing-page" was not found', $exception->getMessage());
        }

        $publicationController = new PagePublicationController(
            $this->pageRepository,
            new PagePublicationService($this->pagePublicationRepository),
            $httpFactory,
        );
        try {
            $publicationController->list('missing-page');
            self::fail('Missing publication page should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertStringContainsString('Page "missing-page" was not found', $exception->getMessage());
        }

        $page = (new PageDraftService($this->pageRepository))->createPage(new PageCreateInputDTO(
            'date-fallback',
            'date-fallback',
            'Date fallback',
            PageKind::Page,
        ));
        (new PageRevisionService($this->pageRevisionRepository))->createRevision($page, new PageRevisionCreateInputDTO(
            'Date fallback',
            '<p>Date fallback</p>',
        ));
        $published = $this->responseArray($publicationController->publish(1, Request::create(
            '/api/page/publication/revision/1?code=date-fallback',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{}',
        )));
        self::assertNull($published['publication']['effectiveFrom']);
        self::assertNull($published['publication']['expiresAt']);

        $authoring = new PageAuthoringController(
            $this->pageRepository,
            new PageDraftService($this->pageRepository),
            $httpFactory,
        );
        $defaultKind = $this->responseArray($authoring->create(Request::create(
            '/api/page/authoring/page',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'code' => 'default-kind',
                'slug' => 'default-kind',
                'title' => 'Default kind',
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame('page', $defaultKind['page']['kind']);

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);
        $acceptanceController = new PageAcceptanceController(
            $this->pageRepository,
            new PageAcceptanceService($this->pageAcceptanceRepository),
        );

        try {
            $acceptanceController->check('subject', Request::create(
                '/api/page/acceptance/revision/1/subject/subject?code=home',
                'GET',
            ));
            self::fail('Missing revision number should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page revision number is required.', $exception->getMessage());
        }

        try {
            $acceptanceController->check('subject', Request::create(
                '/api/page/acceptance/revision/999/subject/subject?code=home&revisionNumber=999',
                'GET',
            ));
            self::fail('Missing acceptance revision should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertStringContainsString('revision 999', $exception->getMessage());
        }

        try {
            $acceptanceController->check('subject', Request::create(
                '/api/page/acceptance/revision/1/subject/subject?code=missing-page&revisionNumber=1',
                'GET',
            ));
            self::fail('Missing acceptance page should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertStringContainsString('Page "missing-page" was not found', $exception->getMessage());
        }

        try {
            $acceptanceController->accept(1, Request::create(
                '/api/page/acceptance/revision/1',
                'POST',
                server: ['CONTENT_TYPE' => 'application/json'],
                content: '',
            ));
            self::fail('Empty acceptance request without page code should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page code is required.', $exception->getMessage());
        }

        $attributeRequest = Request::create('/api/page/acceptance/revision/1/subject/subject?code=home', 'GET');
        $attributeRequest->attributes->set('revisionNumber', 1);
        $attributeCheck = $this->responseArray($acceptanceController->check('subject', $attributeRequest));
        self::assertSame(1, $attributeCheck['revisionNumber']);

        try {
            $acceptanceController->accept(1, Request::create(
                '/api/page/acceptance/revision/1',
                'POST',
                server: ['CONTENT_TYPE' => 'application/json'],
                content: '"scalar"',
            ));
            self::fail('Scalar acceptance payload without page code should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page code is required.', $exception->getMessage());
        }

        $ownerFallback = $this->responseArray($authoring->update('default-kind', Request::create(
            '/api/page/authoring/page/default-kind',
            'PATCH',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['title' => 'Default kind renamed'], JSON_THROW_ON_ERROR),
        )));
        self::assertSame('Default kind renamed', $ownerFallback['page']['title']);

        $ownerString = $this->responseArray($authoring->update('default-kind', Request::create(
            '/api/page/authoring/page/default-kind',
            'PATCH',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['ownerUserId' => 'owner-restored'], JSON_THROW_ON_ERROR),
        )));
        self::assertSame('owner-restored', $ownerString['page']['ownerUserId']);

        $scalarAuthoring = $this->responseArray($authoring->create(Request::create(
            '/api/page/authoring/page',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '"scalar"',
        )));
        self::assertSame('', $scalarAuthoring['page']['code']);
        self::assertSame('page', $scalarAuthoring['page']['kind']);

        try {
            $publicationController->publish(1, Request::create(
                '/api/page/publication/revision/1',
                'POST',
                server: ['CONTENT_TYPE' => 'application/json'],
                content: '"scalar"',
            ));
            self::fail('Scalar publication payload without page code should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page code is required.', $exception->getMessage());
        }

        $blankDatesPage = (new PageDraftService($this->pageRepository))->createPage(new PageCreateInputDTO(
            'blank-dates',
            'blank-dates',
            'Blank dates',
            PageKind::Page,
        ));
        (new PageRevisionService($this->pageRevisionRepository))->createRevision($blankDatesPage, new PageRevisionCreateInputDTO(
            'Blank dates',
            '<p>Blank dates</p>',
        ));
        $blankDates = $this->responseArray($publicationController->publish(1, Request::create(
            '/api/page/publication/revision/1',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'code' => 'blank-dates',
                'effectiveFrom' => '   ',
                'expiresAt' => 123,
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertNull($blankDates['publication']['effectiveFrom']);
        self::assertNull($blankDates['publication']['expiresAt']);

        try {
            $publicationController->publish(1, Request::create(
                '/api/page/publication/revision/1',
                'POST',
                server: ['CONTENT_TYPE' => 'application/json'],
                content: json_encode(['code' => '   '], JSON_THROW_ON_ERROR),
            ));
            self::fail('Blank publication page code should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page code is required.', $exception->getMessage());
        }

        try {
            $acceptanceController->check('subject', Request::create(
                '/api/page/acceptance/revision/0/subject/subject?code=home&revisionNumber=0',
                'GET',
            ));
            self::fail('Zero revision number should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page revision number is required.', $exception->getMessage());
        }

        try {
            $acceptanceController->accept(1, Request::create(
                '/api/page/acceptance/revision/1',
                'POST',
                server: ['CONTENT_TYPE' => 'application/json'],
                content: json_encode([
                    'code' => '   ',
                    'subjectUserId' => 'blank-code-subject',
                ], JSON_THROW_ON_ERROR),
            ));
            self::fail('Blank acceptance page code should fail.');
        } catch (NotFoundHttpException $exception) {
            self::assertSame('Page code is required.', $exception->getMessage());
        }

        $nonArrayContext = $this->responseArray($acceptanceController->accept(2, Request::create(
            '/api/page/acceptance/revision/2',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'code' => 'home',
                'subjectUserId' => 'context-subject',
                'acceptanceContext' => 'not-an-array',
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertNull($nonArrayContext['acceptance']['acceptanceContext']);

        $secondRevisionPublication = $this->responseArray($publicationController->publish(2, Request::create(
            '/api/page/publication/revision/2',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'code' => 'home',
                'publishedByUserId' => 'second-revision-publisher',
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame(2, $secondRevisionPublication['publication']['revisionNumber']);

        $secondRevisionAcceptance = $this->responseArray($acceptanceController->accept(2, Request::create(
            '/api/page/acceptance/revision/2',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'code' => 'home',
                'subjectUserId' => 'second-revision-subject',
            ], JSON_THROW_ON_ERROR),
        )));
        self::assertSame(2, $secondRevisionAcceptance['acceptance']['revisionNumber']);
    }

    /** @return array<string, mixed> */
    private function responseArray(Response $response): array
    {
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($payload);

        return $payload;
    }
}
