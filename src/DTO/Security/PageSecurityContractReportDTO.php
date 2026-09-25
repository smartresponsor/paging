<?php

declare(strict_types=1);

namespace App\Paging\DTO\Security;

final readonly class PageSecurityContractReportDTO
{
    public function __construct(
        public string $grantEntity,
        public string $voterClass,
        public string $subjectResolverInterface,
        public string $grantServiceInterface,
        public array $voterAttributes,
        public array $grantTypes,
        public array $globalRoles,
        public array $hostOwnedSurfaces,
    ) {
    }

    public function passed(): bool
    {
        return [] !== $this->voterAttributes && [] !== $this->grantTypes && [] !== $this->globalRoles && [] !== $this->hostOwnedSurfaces;
    }

    public function toArray(): array
    {
        return [
            'component' => 'paging',
            'consumer' => 'host-security',
            'status' => $this->passed() ? 'ready' : 'incomplete',
            'grantEntity' => $this->grantEntity,
            'voterClass' => $this->voterClass,
            'subjectResolverInterface' => $this->subjectResolverInterface,
            'grantServiceInterface' => $this->grantServiceInterface,
            'voterAttributes' => $this->voterAttributes,
            'grantTypes' => $this->grantTypes,
            'globalRoles' => $this->globalRoles,
            'hostOwnedSurfaces' => $this->hostOwnedSurfaces,
        ];
    }
}
