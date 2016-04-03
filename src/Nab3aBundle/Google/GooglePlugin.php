<?php

namespace Nab3aBundle\Google;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class GooglePlugin extends BundlePlugin
{
    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
          ->children()
              ->scalarNode('client_id')
                  ->isRequired()
              ->end()
              ->scalarNode('client_secret')
                  ->isRequired()
              ->end()
              ->scalarNode('redirect_url')
                  ->isRequired()
              ->end()
              ->scalarNode('developer_key')
                  ->isRequired()
              ->end()
              ->scalarNode('credentials_path')
                  ->isRequired()
              ->end()
              ->scalarNode('client_secret_path')
                  ->isRequired()
              ->end()
              ->scalarNode('script')
                  ->isRequired()
              ->end()
              ->scalarNode('document')
                  ->isRequired()
              ->end()
              ->scalarNode('sheet')
                  ->isRequired()
              ->end()
              ->arrayNode('mapping')
                  ->prototype('scalar')
                  ->end()
              ->end()
        ;
    }
}
