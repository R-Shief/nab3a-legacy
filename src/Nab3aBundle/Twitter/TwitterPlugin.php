<?php

namespace Nab3aBundle\Twitter;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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

        $definition = $container->getDefinition('twitter.guzzle.middleware.oauth');
        $definition->setArguments([[
            'consumer_key' => $pluginConfiguration['consumer_key'],
            'consumer_secret' => $pluginConfiguration['consumer_secret'],
            'token' => $pluginConfiguration['access_token'],
            'token_secret' => $pluginConfiguration['access_token_secret'],
        ]]);

        $definition = $container->getDefinition('twitter.guzzle.stack.configurator');
        $definition->setArguments([new Reference('event_loop'), [
          new Reference('guzzle.middleware.retry'),
          new Reference('guzzle.middleware.log'),
          new Reference('guzzle.middleware.history'),
          new Reference('twitter.guzzle.middleware.oauth'),
        ]]);
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
