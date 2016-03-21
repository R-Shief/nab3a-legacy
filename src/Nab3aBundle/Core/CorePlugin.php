<?php

namespace Nab3aBundle\Core;

use Bangpound\Symfony\DependencyInjection\CallableCompilerPass;
use Matthias\BundlePlugins\BundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class CorePlugin implements BundlePlugin
{
    /**
     * The name of this plugin. It will be used as the configuration key.
     *
     * @return string
     */
    public function name()
    {
        return 'core';
    }

    /**
     * Load this plugin: define services, load service definition files, etc.
     *
     * @param array            $pluginConfiguration The part of the bundle configuration for this plugin
     * @param ContainerBuilder $container
     */
    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('services.yml');
        $loader->load('guzzle.yml');
        $loader->load('console.yml');
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
        $container->addCompilerPass(new CallableCompilerPass(function (ContainerBuilder $container) {
            $definition = $container->getDefinition('console.application');
            $tags = array_filter($container->findTaggedServiceIds('console.command'), function ($tag) {
                $props = array_filter($tag, function ($prop) {
                    return isset($prop['app']) && $prop['app'] === 'nab3a';
                });

                return !empty($props);
            });

            foreach ($tags as $id => $tag) {
                $definition->addMethodCall('add', [new Reference($id)]);
            }
        }));
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
        // TODO: Implement boot() method.
    }
}
