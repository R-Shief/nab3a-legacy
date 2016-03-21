<?php

namespace Nab3aBundle\Stream;

use Matthias\BundlePlugins\BundlePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class StreamPlugin implements BundlePlugin
{
    public function name()
    {
        return 'stream';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('stream.yml');

        foreach ($pluginConfiguration as $key => $value) {
            $container->setParameter('nab3a.stream.'.$key, $value);
        }
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
            ->beforeNormalization()
                ->ifTrue(function ($v) {
                    return !empty(array_filter($v, function ($v) {
                        return !is_array($v);
                    }));
                })
                ->then(function ($v) {
                    return ['default' => $v];
                })
            ->end()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('source')
                        ->isRequired()
                    ->end()
                    ->scalarNode('type')
                        ->isRequired()
                    ->end()
                    ->variableNode('parameters')
                        ->defaultValue([])
                    ->end()
                ->end()
          ->end()
        ;
    }

    public function build(ContainerBuilder $container)
    {
    }

    public function boot(ContainerInterface $container)
    {
        // TODO: Implement boot() method.
    }
}
