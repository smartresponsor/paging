<?php

declare(strict_types=1);

namespace App\Paging\Service\Runtime;

use App\Paging\ServiceInterface\Runtime\PageRuntimeProbeServiceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class PageRuntimeProbeService implements PageRuntimeProbeServiceInterface
{
    public function __construct(private ParameterBagInterface $parameters)
    {
    }

    public function snapshot(): array
    {
        return [
            'component' => 'Paging',
            'namespace' => 'App\\Paging',
            'business_prefix' => 'Page',
            'database_prefix' => 'page_',
            'config_prefix' => 'page',
            'public_route_prefix' => $this->parameters->get('page.public_route_prefix'),
            'api_route_prefix' => $this->parameters->get('page.api_route_prefix'),
            'revision_lock_after_publish' => $this->parameters->get('page.revision_lock_after_publish'),
            'standalone_runtime' => $this->parameters->get('page.standalone_runtime'),
        ];
    }
}
