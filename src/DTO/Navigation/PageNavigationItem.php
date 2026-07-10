<?php

declare(strict_types=1);

namespace App\Paging\DTO\Navigation;

final readonly class PageNavigationItem
{
    /**
     * @param array<string, mixed> $metadata
     * @param list<string>         $visibleForRoles
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $path,
        public string $routeName,
        public string $location,
        public string $operation,
        public int $priority,
        public ?string $icon = null,
        public array $metadata = [],
        public array $visibleForRoles = ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'],
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => 'link',
            'label' => $this->label,
            'icon' => $this->icon,
            'priority' => $this->priority,
            'path' => $this->path,
            'metadata' => array_replace([
                'business_visible' => true,
                'route_name' => $this->routeName,
                'domain' => 'page',
                'operation' => $this->operation,
                'navigation_scope' => 'system',
                'menu_scope' => 'system',
                'admin_only' => true,
                'easyadmin_operator_surface' => true,
                'namespace_provider' => 'App\\Paging\\Controller\\Admin',
            ], $this->metadata),
            'visible_for_roles' => $this->visibleForRoles,
            'location' => $this->location,
            'route_name' => $this->routeName,
        ];
    }

    /** @return array<string, mixed> */
    public function toNavigatingEntitySeed(): array
    {
        $payload = $this->toArray();

        return [
            'navigationKey' => 'paging.'.$this->key,
            'label' => $this->label,
            'slug' => 'paging-'.$this->key,
            'routeName' => $this->routeName,
            'routeParameters' => [],
            'location' => $this->location,
            'operation' => $this->operation,
            'icon' => $this->icon,
            'requiredRole' => 'ROLE_ADMIN',
            'position' => $this->priority,
            'enabled' => true,
            'metadata' => $payload['metadata'],
        ];
    }
}
