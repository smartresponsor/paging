<?php

declare(strict_types=1);

namespace App\Paging\Tests;

use App\Paging\DataFixtures\PagingDemoFixtures;
use App\Paging\Entity\Page;
use App\Paging\Entity\PagePublication;
use App\Paging\Entity\PageRevision;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

final class PagingDemoFixturesContractTest extends TestCase
{
    public function testDemoFixturesPersistIntegerPrimaryKeysAndUuidSlugs(): void
    {
        $entityManager = $this->entityManager();
        (new PagingDemoFixtures())->load($entityManager);

        $pages = $entityManager->createQuery('SELECT page FROM '.Page::class.' page ORDER BY page.code ASC')->getResult();
        $revisions = $entityManager->createQuery('SELECT revision FROM '.PageRevision::class.' revision ORDER BY revision.revisionNumber ASC')->getResult();
        $publications = $entityManager->createQuery('SELECT publication FROM '.PagePublication::class.' publication ORDER BY publication.publishedAt ASC')->getResult();

        self::assertCount(4, $pages);
        self::assertCount(8, $revisions);
        self::assertCount(4, $publications);

        foreach ($pages as $page) {
            self::assertIsInt($page->getId());
            self::assertMatchesRegularExpression('/^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-8[a-f0-9]{3}-[a-f0-9]{12}$/', $page->getSlug());
            self::assertSame(2, $page->getRevisions()->count());
            self::assertNotNull($page->getCurrentRevision());
            self::assertNotNull($page->getPublishedRevision());
            self::assertSame($page->getPublishedRevision(), $page->getCurrentRevision());
            self::assertSame('published', $page->getStatus()->value);
        }

        foreach ($revisions as $revision) {
            self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $revision->getId());
            self::assertGreaterThan(0, $revision->getRevisionNumber());
        }

        foreach ($publications as $publication) {
            self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $publication->getId());
            self::assertSame('published', $publication->getStatus()->value);
        }
    }

    private function entityManager(): EntityManager
    {
        $projectDir = dirname(__DIR__);
        $config = ORMSetup::createAttributeMetadataConfig([$projectDir.'/src/Entity'], true);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());
        $config->enableNativeLazyObjects(true);
        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $entityManager = new EntityManager($connection, $config);
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        return $entityManager;
    }
}
