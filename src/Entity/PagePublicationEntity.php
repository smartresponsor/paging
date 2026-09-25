<?php

declare(strict_types=1);

namespace App\Paging\Entity;

use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Enum\PagePublicationStatus;
use App\Paging\Repository\PagePublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagePublicationRepository::class)]
#[ORM\Table(name: 'page_publication')]
#[ORM\Index(name: 'page_publication_page_idx', columns: ['page_id'])]
#[ORM\Index(name: 'page_publication_status_idx', columns: ['status'])]
#[ORM\Index(name: 'page_publication_revision_idx', columns: ['revision_id'])]
class PagePublicationEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'publications')]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Page $page;

    #[ORM\ManyToOne(targetEntity: PageRevision::class)]
    #[ORM\JoinColumn(name: 'revision_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private PageRevision $revision;

    #[ORM\Column(name: 'published_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $publishedAt;

    #[ORM\Column(name: 'effective_from', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $effectiveFrom = null;

    #[ORM\Column(name: 'expires_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(name: 'published_by_user_id', type: 'string', length: 128, nullable: true)]
    private ?string $publishedByUserId = null;

    #[ORM\Column(type: 'string', length: 32, enumType: PagePublicationStatus::class)]
    private PagePublicationStatus $status;

    public function __construct(Page $page, PageRevision $revision, ?\DateTimeImmutable $effectiveFrom = null, ?\DateTimeImmutable $expiresAt = null, ?string $publishedByUserId = null, PagePublicationStatus $status = PagePublicationStatus::Published)
    {
        $this->id = bin2hex(random_bytes(16));
        $this->page = $page;
        $this->revision = $revision;
        $this->effectiveFrom = $effectiveFrom;
        $this->expiresAt = $expiresAt;
        $this->publishedByUserId = $publishedByUserId;
        $this->status = $status;
        $this->publishedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getRevision(): PageRevision
    {
        return $this->revision;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getEffectiveFrom(): ?\DateTimeImmutable
    {
        return $this->effectiveFrom;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getPublishedByUserId(): ?string
    {
        return $this->publishedByUserId;
    }

    public function getStatus(): PagePublicationStatus
    {
        return $this->status;
    }
}
