<?php

namespace Nab3aBundle\Stream;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StreamPlugin extends BundlePlugin
{
    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        parent::load($pluginConfiguration, $container);
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
}
