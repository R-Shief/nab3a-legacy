<?php

namespace Nab3aBundle\Twitter;

use Bangpound\Symfony\DependencyInjection\CallableCompilerPass;
use Matthias\BundlePlugins\BundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class TwitterPlugin implements BundlePlugin
{
    /**
   * Configures the passed container according to the merged configuration.
   *
   * @param array $mergedConfig
   * @param ContainerBuilder $container
   */
  protected function loadInternal(
    array $mergedConfig,
    ContainerBuilder $container
  ) {
  }

    /**
     * The name of this plugin. It will be used as the configuration key.
     *
     * @return string
     */
    public function name()
    {
        return 'twitter';
    }

    /**
     * Load this plugin: define services, load service definition files, etc.
     *
     * @param array            $pluginConfiguration The part of the bundle configuration for this plugin
     * @param ContainerBuilder $container
     */
    public function load(
      array $pluginConfiguration,
      ContainerBuilder $container
    ) {
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

    /**
     * Add configuration nodes for this plugin to the provided node, e.g.:
     *     $pluginNode
     *         ->children()
     *             ->scalarNode('foo')
     *                 ->isRequired()
     *             ->end()
     *         ->end();.
     *
     * @param ArrayNodeDefinition $pluginNode
     */
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

    /**
     * When the container is generated for the first time, you can register compiler passes inside this method.
     *
     * @see BundleInterface::build()
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
    }

    /**
     * When the bundles are booted, you can do any runtime initialization required inside this method.
     *
     * @see BundleInterface::boot()
     *
     * @param ContainerInterface $container
     */
    public function boot(ContainerInterface $container)
    {
    }
}
