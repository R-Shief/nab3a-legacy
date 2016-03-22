<?php

namespace Nab3aBundle\Twitter;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class TwitterPlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'twitter';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('twitter.yml');
    }

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
