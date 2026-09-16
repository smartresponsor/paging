<?php

declare(strict_types=1);

namespace App\Paging\Tests\Integration;

use App\Paging\Controller\Api\PageExportController;
use App\Paging\Controller\Public\PageViewController;
use App\Paging\DataFixtures\PagingDemoFixtures;
use App\Paging\DTO\Authoring\PageCreateInput;
use App\Paging\DTO\Publication\PagePublishInput;
use App\Paging\DTO\Revision\PageRevisionCreateInput;
use App\Paging\Enum\PageKind;
use App\Paging\Repository\PageRepository;
use App\Paging\Service\Authoring\PageDraftService;
use App\Paging\Service\Bridge\PageBridgeContractProvider;
use App\Paging\Service\Bridge\PageBridgePayloadFactory;
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
        (new PagingDemoFixtures())->load($this->entityManager);

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);
        $this->pageRepository = new PageRepository($registry);
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
        $draftService = new PageDraftService($this->entityManager);
        $revisionService = new PageRevisionService($this->entityManager);
        $publicationService = new PagePublicationService($this->entityManager);

        $page = $draftService->createPage(new PageCreateInput('faq', 'faq', 'FAQ', PageKind::Help, 'owner-9'));
        $revision = $revisionService->createRevision($page, new PageRevisionCreateInput(
            'FAQ',
            '<h1>FAQ</h1><p>Answer</p>',
            bodyMarkdown: "# FAQ\n\nAnswer",
            changeNote: 'Initial FAQ',
            createdByUserId: 'owner-9',
        ));
        $publication = $publicationService->publishRevision($revision, new PagePublishInput(
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
}
