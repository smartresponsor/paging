<?php

declare(strict_types=1);

namespace App\Paging\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('page');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('public_route_prefix')->defaultValue('/page')->end()
                ->scalarNode('api_route_prefix')->defaultValue('/api/page')->end()
                ->booleanNode('revision_lock_after_publish')->defaultTrue()->end()
                ->booleanNode('standalone_runtime')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
