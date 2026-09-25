<?php

declare(strict_types=1);

namespace App\Paging\Tests\Unit;

use App\Paging\DTO\Bridge\PageBridgeAttachmentDTO;
use App\Paging\DTO\Bridge\PageBridgeLegalNoticeDTO;
use App\Paging\DTO\Bridge\PageBridgePayloadDTO;
use App\Paging\DTO\Bridge\PageBridgeRenderHintsDTO;
use App\Paging\Enum\PageAttachmentUsage;
use App\Paging\Enum\PageKind;
use App\Paging\Enum\PageStatus;
use PHPUnit\Framework\TestCase;

final class PageBridgePayloadFactoryTest extends TestCase
{
    public function testBridgePayloadSerializesStableVisualBoundary(): void
    {
        $payload = new PageBridgePayloadDTO(
            'privacy_policy',
            'privacy-policy',
            'Privacy Policy',
            PageKind::Policy,
            PageStatus::Published,
            3,
            '<h1>Privacy Policy</h1>',
            'Privacy Policy',
            '# Privacy Policy',
            ['type' => 'doc'],
            str_repeat('a', 64),
            [new PageBridgeAttachmentDTO('attachment-1', PageAttachmentUsage::Download, 'privacy-pdf', 1)],
            new PageBridgeRenderHintsDTO(preferredTemplateKey: 'page/policy', legalMode: true),
            new PageBridgeLegalNoticeDTO('Version 3', requiresAcceptance: true, acceptanceRevisionNumber: 3),
        );

        $data = $payload->toArray();

        self::assertSame('privacy_policy', $data['code']);
        self::assertSame('policy', $data['kind']);
        self::assertSame('published', $data['status']);
        self::assertSame(3, $data['revisionNumber']);
        self::assertSame('page/policy', $data['renderHints']['preferredTemplateKey']);
        self::assertTrue($data['renderHints']['legalMode']);
        self::assertTrue($data['legalNotice']['requiresAcceptance']);
        self::assertSame('privacy-pdf', $data['attachments'][0]['attachmentCode']);
    }
}
