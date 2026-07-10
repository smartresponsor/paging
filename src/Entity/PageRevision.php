<?php

declare(strict_types=1);

namespace App\Paging\Entity;

use App\Paging\Repository\PageRevisionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRevisionRepository::class)]
#[ORM\Table(name: 'page_revision')]
#[ORM\Index(name: 'page_revision_page_idx', columns: ['page_id'])]
#[ORM\UniqueConstraint(name: 'page_revision_number_uniq', columns: ['page_id', 'revision_number'])]
class PageRevision
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'revisions')]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Page $page;

    #[ORM\Column(name: 'revision_number', type: 'integer')]
    private int $revisionNumber;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(name: 'body_html', type: 'text')]
    private string $bodyHtml;

    #[ORM\Column(name: 'body_markdown', type: 'text', nullable: true)]
    private ?string $bodyMarkdown = null;

    #[ORM\Column(name: 'body_json', type: 'json', nullable: true)]
    private ?array $bodyJson = null;

    #[ORM\Column(name: 'body_text', type: 'text')]
    private string $bodyText;

    #[ORM\Column(name: 'change_note', type: 'text', nullable: true)]
    private ?string $changeNote = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $checksum;

    #[ORM\Column(name: 'created_by_user_id', type: 'string', length: 128, nullable: true)]
    private ?string $createdByUserId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'locked_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lockedAt = null;
    #[ORM\Column(name: 'locked_by', type: 'string', length: 128, nullable: true)]
    private ?string $lockedBy = null;

    public function __construct(Page $page, int $revisionNumber, string $title, string $bodyHtml, string $bodyText, ?string $bodyMarkdown = null, ?array $bodyJson = null, ?string $changeNote = null, ?string $createdByUserId = null)
    {
        $this->id = bin2hex(random_bytes(16));
        $this->page = $page;
        $this->revisionNumber = $revisionNumber;
        $this->title = $title;
        $this->bodyHtml = $bodyHtml;
        $this->bodyText = $bodyText;
        $this->bodyMarkdown = $bodyMarkdown;
        $this->bodyJson = $bodyJson;
        $this->changeNote = $changeNote;
        $this->createdByUserId = $createdByUserId;
        $this->checksum = hash('sha256', implode("\n---page-revision---\n", [$title, $bodyHtml, $bodyText, $bodyMarkdown ?? '', json_encode($bodyJson, JSON_THROW_ON_ERROR)]));
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

    public function getRevisionNumber(): int
    {
        return $this->revisionNumber;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBodyHtml(): string
    {
        return $this->bodyHtml;
    }

    public function getBodyMarkdown(): ?string
    {
        return $this->bodyMarkdown;
    }

    public function getBodyJson(): ?array
    {
        return $this->bodyJson;
    }

    public function getBodyText(): string
    {
        return $this->bodyText;
    }

    public function getChangeNote(): ?string
    {
        return $this->changeNote;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }

    public function getCreatedByUserId(): ?string
    {
        return $this->createdByUserId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return sprintf('%s revision %d', $this->page->getCode(), $this->revisionNumber);
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->getCreatedAt();
    }

    public function getLockedAt(): ?\DateTimeImmutable
    {
        return $this->lockedAt;
    }

    public function lockedAt(): ?\DateTimeImmutable
    {
        return $this->getLockedAt();
    }

    public function getLockedBy(): ?string
    {
        return $this->lockedBy;
    }

    public function lockedBy(): ?string
    {
        return $this->getLockedBy();
    }

    public function isLocked(): bool
    {
        return null !== $this->lockedAt;
    }

    public function lock(?string $lockedBy = null, ?\DateTimeImmutable $lockedAt = null): void
    {
        $this->lockedBy = $lockedBy;
        $this->lockedAt = $lockedAt ?? new \DateTimeImmutable();
    }

    public function unlock(): void
    {
        $this->lockedAt = null;
        $this->lockedBy = null;
    }
}
