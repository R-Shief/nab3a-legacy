<?php

namespace AppBundle\Twitter;

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
        $rootNode = $treeBuilder->root('twitter');

        $rootNode
            ->canBeEnabled()
            ->children()
                ->scalarNode('consumer_key')
                    ->isRequired()
                ->end()
                ->scalarNode('consumer_secret')
                   ->isRequired()
                ->end()
                ->scalarNode('access_token')
                    ->isRequired()
                ->end()
                ->scalarNode('access_token_secret')
                    ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
