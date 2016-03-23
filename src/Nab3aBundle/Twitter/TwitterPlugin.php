<?php

namespace Nab3aBundle\Twitter;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class TwitterPlugin extends BundlePlugin
{
    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
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
          ->end();
    }
}
