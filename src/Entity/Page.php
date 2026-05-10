<?php

declare(strict_types=1);

namespace App\Paging\Entity;

use App\Paging\Enum\PageKind;
use App\Paging\Enum\PageStatus;
use App\Paging\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'page')]
#[ORM\Index(name: 'page_kind_idx', columns: ['kind'])]
#[ORM\Index(name: 'page_status_idx', columns: ['status'])]
#[ORM\UniqueConstraint(name: 'page_code_uniq', columns: ['code'])]
#[ORM\UniqueConstraint(name: 'page_slug_uniq', columns: ['slug'])]
class Page
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 32, enumType: PageKind::class)]
    private PageKind $kind;

    #[ORM\Column(type: 'string', length: 32, enumType: PageStatus::class)]
    private PageStatus $status = PageStatus::Draft;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $ownerUserId = null;

    #[ORM\ManyToOne(targetEntity: PageRevision::class)]
    #[ORM\JoinColumn(name: 'current_revision_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?PageRevision $currentRevision = null;

    #[ORM\ManyToOne(targetEntity: PageRevision::class)]
    #[ORM\JoinColumn(name: 'published_revision_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?PageRevision $publishedRevision = null;

    /** @var Collection<int, PageRevision> */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: PageRevision::class, cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['revisionNumber' => 'DESC'])]
    private Collection $revisions;

    /** @var Collection<int, PagePublication> */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: PagePublication::class, cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['publishedAt' => 'DESC'])]
    private Collection $publications;

    /** @var Collection<int, PageAttachmentReference> */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: PageAttachmentReference::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $attachmentReferences;

    /** @var Collection<int, PageGrant> */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: PageGrant::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $grants;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $code, string $slug, string $title, PageKind $kind = PageKind::Page, ?string $ownerUserId = null)
    {
        $now = new \DateTimeImmutable();
        $this->id = self::newId();
        $this->code = $code;
        $this->slug = $slug;
        $this->title = $title;
        $this->kind = $kind;
        $this->ownerUserId = $ownerUserId;
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->revisions = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->attachmentReferences = new ArrayCollection();
        $this->grants = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getKind(): PageKind
    {
        return $this->kind;
    }

    public function getStatus(): PageStatus
    {
        return $this->status;
    }

    public function getOwnerUserId(): ?string
    {
        return $this->ownerUserId;
    }

    public function getCurrentRevision(): ?PageRevision
    {
        return $this->currentRevision;
    }

    public function getPublishedRevision(): ?PageRevision
    {
        return $this->publishedRevision;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @return Collection<int, PageRevision> */
    public function getRevisions(): Collection
    {
        return $this->revisions;
    }

    /** @return Collection<int, PagePublication> */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    /** @return Collection<int, PageAttachmentReference> */
    public function getAttachmentReferences(): Collection
    {
        return $this->attachmentReferences;
    }

    /** @return Collection<int, PageGrant> */
    public function getGrants(): Collection
    {
        return $this->grants;
    }

    public function rename(string $title, string $slug): void
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->touch();
    }

    public function assignOwner(?string $ownerUserId): void
    {
        $this->ownerUserId = $ownerUserId;
        $this->touch();
    }

    public function useCurrentRevision(PageRevision $revision): void
    {
        $this->currentRevision = $revision;
        if (!$this->revisions->contains($revision)) {
            $this->revisions->add($revision);
        }
        $this->touch();
    }

    public function markPublished(PageRevision $revision): void
    {
        $this->publishedRevision = $revision;
        $this->status = PageStatus::Published;
        $this->useCurrentRevision($revision);
    }

    public function archive(): void
    {
        $this->status = PageStatus::Archived;
        $this->touch();
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    private static function newId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
