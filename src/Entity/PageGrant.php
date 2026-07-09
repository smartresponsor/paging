<?php

declare(strict_types=1);

namespace App\Paging\Entity;

use App\Paging\Enum\PageGrantType;
use App\Paging\Repository\PageGrantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageGrantRepository::class)]
#[ORM\Table(name: 'page_grant')]
#[ORM\Index(name: 'page_grant_page_idx', columns: ['page_id'])]
#[ORM\Index(name: 'page_grant_user_idx', columns: ['subject_user_id'])]
#[ORM\Index(name: 'page_grant_role_idx', columns: ['subject_role'])]
class PageGrant
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'grants')]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Page $page;

    #[ORM\Column(name: 'subject_user_id', type: 'string', length: 128, nullable: true)]
    private ?string $subjectUserId = null;

    #[ORM\Column(name: 'subject_role', type: 'string', length: 128, nullable: true)]
    private ?string $subjectRole = null;

    #[ORM\Column(name: 'grant_type', type: 'string', length: 32, enumType: PageGrantType::class)]
    private PageGrantType $grant;

    #[ORM\Column(name: 'created_by_user_id', type: 'string', length: 128, nullable: true)]
    private ?string $createdByUserId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(Page $page, PageGrantType $grant, ?string $subjectUserId = null, ?string $subjectRole = null, ?string $createdByUserId = null)
    {
        $this->id = bin2hex(random_bytes(16));
        $this->page = $page;
        $this->grant = $grant;
        $this->subjectUserId = $subjectUserId;
        $this->subjectRole = $subjectRole;
        $this->createdByUserId = $createdByUserId;
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

    public function getSubjectUserId(): ?string
    {
        return $this->subjectUserId;
    }

    public function getSubjectRole(): ?string
    {
        return $this->subjectRole;
    }

    public function getGrant(): PageGrantType
    {
        return $this->grant;
    }

    public function getCreatedByUserId(): ?string
    {
        return $this->createdByUserId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
