<?php

declare(strict_types=1);

namespace App\Paging\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PageExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $templateDir = realpath(__DIR__.'/../../templates');
        if (false === $templateDir) {
            return;
        }

        $container->prependExtensionConfig('twig', [
            'paths' => [
                $templateDir => null,
            ],
        ]);
    }

    /**
     * Loads the page component configuration and exposes normalized page parameters.
     *
     * @param array<int, array<string, mixed>> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $nameEntity => $value) {
            $container->setParameter('page.'.$nameEntity, $value);
        }

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 2).'/config'));
        if (is_file(dirname(__DIR__, 2).'/config/services.yaml')) {
            $loader->load('services.yaml');
        }
    }

    public function getAlias(): string
    {
        return 'page';
    }
}
