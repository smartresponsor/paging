<?php

declare(strict_types=1);

namespace App\Paging\Entity;

use App\Objecting\EntityInterface\ObjectEntityInterface;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectIdentityEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectStateEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectTitleEmbeddableTrait;
use App\Paging\Entity\PageAttachmentReferenceEntity as PageAttachmentReference;
use App\Paging\Entity\PageGrantEntity as PageGrant;
use App\Paging\Entity\PagePublicationEntity as PagePublication;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Enum\PageKind;
use App\Paging\Enum\PageStatus;
use App\Paging\Repository\PageRepository;
use App\Paging\ValueObject\PageSlug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'page')]
#[ORM\Index(name: 'page_kind_idx', columns: ['kind'])]
#[ORM\Index(name: 'page_status_idx', columns: ['status'])]
#[ORM\UniqueConstraint(name: 'page_code_uniq', columns: ['code'])]
class PageEntity implements ObjectEntityInterface
{
    use ObjectIdentityEmbeddableTrait;
    use ObjectTitleEmbeddableTrait;
    use ObjectAuditEmbeddableTrait;
    use ObjectStateEmbeddableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 32, enumType: PageKind::class)]
    private PageKind $kind;

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

    private string $draftBodyHtml = '';

    private ?string $draftChangeNote = null;

    public function __construct(string $code, string $slug, string $title, PageKind $kind = PageKind::Page, ?string $ownerUserId = null)
    {
        $now = new \DateTimeImmutable();
        $this->code = $code;
        $normalizedSlug = PageSlug::fromSource($slug)->value();
        $this->title = $title;
        $this->kind = $kind;
        $this->ownerUserId = $ownerUserId;
        $this->initializeObjectIdentity(objectSlug: $normalizedSlug);
        $this->initializeObjectTitle($title);
        $this->initializeObjectAudit($now, $ownerUserId);
        $this->touchModified($now, $ownerUserId);
        $this->initializeObjectState(objectStatus: PageStatus::Draft->value);
        $this->revisions = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->attachmentReferences = new ArrayCollection();
        $this->grants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSlug(): string
    {
        return $this->getObjectSlug();
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
        return PageStatus::from($this->getObjectStatus() ?? PageStatus::Draft->value);
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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->getModifiedAt() ?? $this->getCreatedAt();
    }

    public function getDraftBodyHtml(): string
    {
        if ('' !== $this->draftBodyHtml) {
            return $this->draftBodyHtml;
        }

        return $this->currentRevision?->getBodyHtml() ?? '';
    }

    public function setDraftBodyHtml(string $draftBodyHtml): void
    {
        $this->draftBodyHtml = $draftBodyHtml;
    }

    public function getDraftChangeNote(): ?string
    {
        return $this->draftChangeNote;
    }

    public function setDraftChangeNote(?string $draftChangeNote): void
    {
        $draftChangeNote = null === $draftChangeNote ? null : trim($draftChangeNote);
        $this->draftChangeNote = '' === $draftChangeNote ? null : $draftChangeNote;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->title, $this->code);
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
        $normalizedSlug = PageSlug::fromSource($slug)->value();
        $this->setFirstTitle($title);
        $this->setObjectSlug($normalizedSlug);
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
        $this->setObjectStatus(PageStatus::Published->value);
        $this->useCurrentRevision($revision);
    }

    public function archive(): void
    {
        $this->setObjectStatus(PageStatus::Archived->value);
        $this->setObjectActive(false);
        $this->touch();
    }

    private function touch(): void
    {
        $this->touchModified();
    }
}
