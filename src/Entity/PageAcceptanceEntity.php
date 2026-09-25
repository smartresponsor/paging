<?php

declare(strict_types=1);

namespace App\Paging\Entity;

use App\Paging\Entity\PageEntity as Page;
use App\Paging\Entity\PageRevisionEntity as PageRevision;
use App\Paging\Repository\PageAcceptanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageAcceptanceRepository::class)]
#[ORM\Table(name: 'page_acceptance')]
#[ORM\Index(name: 'page_acceptance_page_idx', columns: ['page_id'])]
#[ORM\Index(name: 'page_acceptance_revision_idx', columns: ['revision_id'])]
#[ORM\Index(name: 'page_acceptance_subject_idx', columns: ['subject_user_id'])]
#[ORM\Index(name: 'page_acceptance_checksum_idx', columns: ['revision_checksum'])]
class PageAcceptanceEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Page $page;

    #[ORM\ManyToOne(targetEntity: PageRevision::class)]
    #[ORM\JoinColumn(name: 'revision_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private PageRevision $revision;

    #[ORM\Column(name: 'subject_user_id', type: 'string', length: 128)]
    private string $subjectUserId;

    #[ORM\Column(name: 'revision_checksum', type: 'string', length: 64)]
    private string $revisionChecksum;

    #[ORM\Column(name: 'accepted_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $acceptedAt;

    #[ORM\Column(name: 'ip_hash', type: 'string', length: 64, nullable: true)]
    private ?string $ipHash = null;

    #[ORM\Column(name: 'user_agent_hash', type: 'string', length: 64, nullable: true)]
    private ?string $userAgentHash = null;

    /** @var array<string, mixed>|null */
    #[ORM\Column(name: 'acceptance_context', type: 'json', nullable: true)]
    private ?array $acceptanceContext = null;

    /** @param array<string, mixed>|null $acceptanceContext */
    public function __construct(Page $page, PageRevision $revision, string $subjectUserId, ?string $ipHash = null, ?string $userAgentHash = null, ?array $acceptanceContext = null)
    {
        $this->id = bin2hex(random_bytes(16));
        $this->page = $page;
        $this->revision = $revision;
        $this->subjectUserId = $subjectUserId;
        $this->revisionChecksum = $revision->getChecksum();
        $this->acceptedAt = new \DateTimeImmutable();
        $this->ipHash = $ipHash;
        $this->userAgentHash = $userAgentHash;
        $this->acceptanceContext = $acceptanceContext;
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

    public function getSubjectUserId(): string
    {
        return $this->subjectUserId;
    }

    public function getRevisionChecksum(): string
    {
        return $this->revisionChecksum;
    }

    public function getAcceptedAt(): \DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function getIpHash(): ?string
    {
        return $this->ipHash;
    }

    public function getUserAgentHash(): ?string
    {
        return $this->userAgentHash;
    }

    /** @return array<string, mixed>|null */
    public function getAcceptanceContext(): ?array
    {
        return $this->acceptanceContext;
    }
}
