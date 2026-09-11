<?php

declare(strict_types=1);

namespace App\Paging;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PageBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtensionClass(): string
    {
        return DependencyInjection\PageExtension::class;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
