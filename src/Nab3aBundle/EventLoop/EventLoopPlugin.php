<?php

namespace Nab3aBundle\EventLoop;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Nab3aBundle\Evenement\AttachPluginsCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EventLoopPlugin extends SimpleBundlePlugin
{
    /**
     * The name of this plugin. It will be used as the configuration key.
     *
     * @return string
     */
    public function name()
    {
        return 'event_loop';
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
        $loader->load('event_loop.yml');
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AttachPluginsCompilerPass('nab3a.event_loop.configurator', 'event_loop.plugin'));
    }
}
