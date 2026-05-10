<?php

declare(strict_types=1);

namespace App\Paging\Entity;

use App\Paging\Enum\PageAttachmentUsage;
use App\Paging\Repository\PageAttachmentReferenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageAttachmentReferenceRepository::class)]
#[ORM\Table(name: 'page_attachment_reference')]
#[ORM\Index(name: 'page_attachment_reference_page_idx', columns: ['page_id'])]
#[ORM\Index(name: 'page_attachment_reference_attachment_idx', columns: ['attachment_id'])]
class PageAttachmentReference
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'attachmentReferences')]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Page $page;

    #[ORM\ManyToOne(targetEntity: PageRevision::class)]
    #[ORM\JoinColumn(name: 'revision_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?PageRevision $revision = null;

    #[ORM\Column(name: 'attachment_id', type: 'string', length: 128)]
    private string $attachmentId;

    #[ORM\Column(name: 'attachment_code', type: 'string', length: 128, nullable: true)]
    private ?string $attachmentCode = null;

    #[ORM\Column(type: 'string', length: 32, enumType: PageAttachmentUsage::class)]
    private PageAttachmentUsage $usage;

    #[ORM\Column(type: 'integer')]
    private int $position = 0;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(Page $page, string $attachmentId, PageAttachmentUsage $usage = PageAttachmentUsage::Inline, ?PageRevision $revision = null, ?string $attachmentCode = null, int $position = 0)
    {
        $this->id = bin2hex(random_bytes(16));
        $this->page = $page;
        $this->revision = $revision;
        $this->attachmentId = $attachmentId;
        $this->attachmentCode = $attachmentCode;
        $this->usage = $usage;
        $this->position = $position;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getRevision(): ?PageRevision
    {
        return $this->revision;
    }

    public function getAttachmentId(): string
    {
        return $this->attachmentId;
    }

    public function getAttachmentCode(): ?string
    {
        return $this->attachmentCode;
    }

    public function getUsage(): PageAttachmentUsage
    {
        return $this->usage;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
