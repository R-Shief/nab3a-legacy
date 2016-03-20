<?php

namespace Nab3aBundle\Nab3a;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nab3a');

        $rootNode
            ->canBeEnabled()
            ->children()
                ->scalarNode('type')
                    ->isRequired()
                ->end()
                ->arrayNode('track')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('follow')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('locations')
                    ->prototype('variable')
                    ->end()
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
