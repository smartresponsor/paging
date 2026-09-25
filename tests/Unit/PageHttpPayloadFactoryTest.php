<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Export\PageExportViewDTO;
use App\Paging\DTO\Rendering\PageRenderViewDTO;
use App\Paging\DTO\Security\PageGrantCheckDTO;
use App\Paging\DTO\Security\PageGrantInputDTO;
use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;
use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Enum\PageAttachmentUsage;
use App\Paging\Enum\PageExportFormat;
use App\Paging\Enum\PageGrantType;
use App\Paging\Enum\PageKind;
use App\Paging\Enum\PageStatus;
use App\Paging\Factory\Bridge\PageApiBridgePayloadFactory;
use App\Paging\Factory\Bridge\PageBridgePayloadFactory;
use App\Paging\Factory\Http\PageHttpPayloadFactory;
use App\Paging\RepositoryInterface\PageGrantRepositoryInterface;
use App\Paging\Service\Rendering\PageRenderService;
use App\Paging\Service\Security\PageGrantService;
use PHPUnit\Framework\TestCase;

final class PageHttpPayloadFactoryTest extends TestCase
{
    public function testRenderViewPayloadUsesScalarValuesForHttpBoundary(): void
    {
        $factory = new PageHttpPayloadFactory();
        $payload = $factory->renderViewToArray(new PageRenderViewDTO(
            'privacy_policy',
            'privacy-policy',
            'Privacy Policy',
            PageKind::Policy,
            2,
            '<p>Policy</p>',
            'Policy',
            '# Policy',
            str_repeat('a', 64),
            new \DateTimeImmutable('2026-05-04T10:00:00-05:00'),
            new \DateTimeImmutable('2026-05-10T00:00:00-05:00'),
        ));

        self::assertSame('policy', $payload['kind']);
        self::assertSame(2, $payload['version']);
        self::assertSame('2026-05-04T10:00:00-05:00', $payload['publishedAt']);
        self::assertSame('2026-05-10T00:00:00-05:00', $payload['effectiveFrom']);
    }

    public function testEntityPayloadsExposeStableScalarBoundary(): void
    {
        $factory = new PageHttpPayloadFactory();
        $page = new Page('terms', 'Terms Of Service', 'Terms', PageKind::Policy, 'owner-1');
        $revision = new PageRevision($page, 3, 'Terms v3', '<p>Terms</p>', 'Terms', '# Terms', null, 'clarify', 'editor-1');
        $revision->lock('editor-2', new \DateTimeImmutable('2026-09-15T12:00:00-05:00'));
        $page->markPublished($revision);
        $publication = new PagePublication($page, $revision, new \DateTimeImmutable('2026-09-16T00:00:00-05:00'), new \DateTimeImmutable('2027-09-16T00:00:00-05:00'), 'publisher-1');

        $pagePayload = $factory->pageToArray($page);
        $revisionPayload = $factory->revisionToArray($revision);
        $publicationPayload = $factory->publicationToArray($publication);

        self::assertSame('terms', $pagePayload['code']);
        self::assertSame('terms-of-service', $pagePayload['slug']);
        self::assertSame('published', $pagePayload['status']);
        self::assertSame(3, $pagePayload['publishedRevision']);
        self::assertSame(3, $revisionPayload['revisionNumber']);
        self::assertTrue($revisionPayload['locked']);
        self::assertSame('2026-09-15T12:00:00-05:00', $revisionPayload['lockedAt']);
        self::assertSame(3, $publicationPayload['revisionNumber']);
        self::assertSame('2026-09-16T00:00:00-05:00', $publicationPayload['effectiveFrom']);
        self::assertSame('2027-09-16T00:00:00-05:00', $publicationPayload['expiresAt']);
    }

    public function testExportPayloadContainsContractMetadata(): void
    {
        $factory = new PageHttpPayloadFactory();
        $payload = $factory->exportViewToArray(new PageExportViewDTO('privacy', 'privacy', 'Privacy', PageExportFormat::Markdown, '# Privacy', 'text/markdown', str_repeat('b', 64)));

        self::assertSame('markdown', $payload['format']);
        self::assertSame('text/markdown', $payload['contentType']);
        self::assertSame(str_repeat('b', 64), $payload['checksum']);
    }

    public function testRenderServiceCoversPublishedDraftAndMissingPublicationPaths(): void
    {
        $service = new PageRenderService();
        $page = new Page('privacy', 'privacy', 'Privacy', PageKind::Policy);
        $revision = new PageRevision($page, 2, 'Privacy v2', '<p>Body</p>', 'Body', '# Body');
        $page->markPublished($revision);
        $publication = new PagePublication($page, $revision, new \DateTimeImmutable('2026-10-01T00:00:00+00:00'));
        $page->getPublications()->add($publication);

        $published = $service->renderPublished($page);
        self::assertSame(2, $published->version);
        self::assertSame($publication->getPublishedAt(), $published->publishedAt);

        $draftPage = new Page('help', 'help', 'Help', PageKind::Help);
        $draftRevision = new PageRevision($draftPage, 1, 'Help', '<p>Help</p>', 'Help');
        $draft = $service->renderRevision($draftRevision);
        self::assertNull($draft->publishedAt);
        self::assertNull($draft->effectiveFrom);
    }

    public function testRenderPublishedRejectsPageWithoutPublishedRevision(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Page "draft" does not have a published revision.');

        (new PageRenderService())->renderPublished(new Page('draft', 'draft', 'Draft'));
    }

    public function testBridgeFactoriesComposeAttachmentsAndLegalHintsAcrossApiAndInterfacingBoundaries(): void
    {
        $page = new Page('privacy', 'privacy', 'Privacy', PageKind::Policy);
        $first = new PageRevision($page, 1, 'Privacy v1', '<p>Old</p>', 'Old');
        $revision = new PageRevision($page, 2, 'Privacy v2', '<p>Policy</p>', 'Policy', '# Policy');
        $page->markPublished($revision);
        $page->getPublications()->add(new PagePublication($page, $first, new \DateTimeImmutable('2026-09-01T00:00:00+00:00')));
        $publication = new PagePublication(
            $page,
            $revision,
            new \DateTimeImmutable('2026-09-02T00:00:00+00:00'),
            new \DateTimeImmutable('2027-09-02T00:00:00+00:00'),
        );
        $page->getPublications()->add($publication);
        $page->getAttachmentReferences()->add(new PageAttachmentReference(
            $page,
            'attachment-1',
            PageAttachmentUsage::LegalSupport,
            $revision,
            'terms-pdf',
            3,
        ));

        $renderer = new PageRenderService();
        $api = (new PageApiBridgePayloadFactory($renderer))->createForPublishedPage($page);
        self::assertTrue($api->renderHints['show_version']);
        self::assertCount(1, $api->attachments);

        $bridge = (new PageBridgePayloadFactory($renderer))->createForPublishedPage($page);
        self::assertTrue($bridge->renderHints->legalMode);
        self::assertCount(1, $bridge->attachments);
        self::assertSame('2027-09-02T00:00:00+00:00', $bridge->expiresAt?->format(DATE_ATOM));
    }

    public function testRichBridgeFactoryRejectsDraftPage(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('does not have a published revision for bridge output');

        (new PageBridgePayloadFactory(new PageRenderService()))->createForPublishedPage(
            new Page('draft-bridge', 'draft-bridge', 'Draft bridge', PageKind::Rule),
        );
    }

    public function testGrantServicePersistsAndResolvesAuthorizationBranches(): void
    {
        $page = new Page('policy', 'policy', 'Policy', ownerUserId: 'owner-1');
        $repository = $this->createMock(PageGrantRepositoryInterface::class);
        $repository->expects(self::once())->method('save')->with(self::isInstanceOf(PageGrant::class));
        $service = new PageGrantService($repository);

        $grant = $service->grant(new PageGrantInputDTO($page, PageGrantType::Edit, 'user-2', null, 'admin-1'));
        self::assertSame('user-2', $grant->getSubjectUserId());
        self::assertTrue($service->isGranted(new PageGrantCheckDTO($page, PageGrantType::Publish, null, ['ROLE_PAGE_ADMIN'])));
        self::assertTrue($service->isGranted(new PageGrantCheckDTO($page, PageGrantType::Edit, 'owner-1')));
        self::assertFalse($service->isGranted(new PageGrantCheckDTO($page, PageGrantType::Publish, 'owner-1')));

        $page->getGrants()->add(new PageGrant($page, PageGrantType::Publish, 'publisher-1'));
        $page->getGrants()->add(new PageGrant($page, PageGrantType::Manage, null, 'ROLE_LEGAL'));
        self::assertTrue($service->isGranted(new PageGrantCheckDTO($page, PageGrantType::Publish, 'publisher-1')));
        self::assertTrue($service->isGranted(new PageGrantCheckDTO($page, PageGrantType::Edit, null, ['ROLE_LEGAL'])));
        self::assertFalse($service->isGranted(new PageGrantCheckDTO($page, PageGrantType::Own, 'outsider')));
    }

    public function testPageRevisionAndPublicationLifecycleAccessors(): void
    {
        $page = new Page('legal', ' Legal Notice ', 'Legal Notice', PageKind::Policy, 'owner-1');
        self::assertNull($page->getId());
        self::assertSame('legal-notice', $page->getSlug());
        self::assertSame(PageStatus::Draft, $page->getStatus());
        self::assertSame('', $page->getDraftBodyHtml());

        $page->setDraftBodyHtml('<p>Draft</p>');
        $page->setDraftChangeNote('  legal update  ');
        self::assertSame('legal update', $page->getDraftChangeNote());
        $page->setDraftChangeNote('   ');
        self::assertNull($page->getDraftChangeNote());
        $page->rename('Updated Legal', 'updated legal');
        $page->assignOwner('owner-2');
        self::assertSame('Updated Legal (legal)', (string) $page);

        $revision = new PageRevision($page, 1, 'Terms v1', '<p>Terms</p>', 'Terms', '# Terms', ['blocks' => []], 'initial', 'author-1');
        self::assertSame(['blocks' => []], $revision->getBodyJson());
        self::assertSame('initial', $revision->getChangeNote());
        self::assertSame('author-1', $revision->getCreatedByUserId());
        self::assertSame(64, strlen($revision->getChecksum()));
        self::assertSame('legal revision 1', (string) $revision);
        $revision->setTitle('Terms v1.1');
        self::assertSame('Terms v1.1', $revision->getTitle());

        $lockedAt = new \DateTimeImmutable('2026-09-15T12:30:00+00:00');
        $revision->lock('editor-1', $lockedAt);
        self::assertTrue($revision->isLocked());
        self::assertSame($lockedAt, $revision->lockedAt());
        self::assertSame('editor-1', $revision->lockedBy());
        $revision->unlock();
        self::assertFalse($revision->isLocked());

        $page->useCurrentRevision($revision);
        self::assertCount(1, $page->getRevisions());
        $page->markPublished($revision);
        self::assertSame(PageStatus::Published, $page->getStatus());

        $publication = new PagePublication($page, $revision, new \DateTimeImmutable('2026-09-20T00:00:00+00:00'), new \DateTimeImmutable('2027-09-20T00:00:00+00:00'), 'publisher-1');
        self::assertSame($page, $publication->getPage());
        self::assertSame($revision, $publication->getRevision());
        self::assertSame('publisher-1', $publication->getPublishedByUserId());
        self::assertSame('published', $publication->getStatus()->value);
        self::assertCount(0, $page->getAttachmentReferences());

        $page->archive();
        self::assertSame(PageStatus::Archived, $page->getStatus());
    }
}
