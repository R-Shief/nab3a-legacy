<?php

namespace Nab3aBundle\Evenement;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EvenementPlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'evenement';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('evenement.yml');
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AttachPluginsCompilerPass('nab3a.evenement.configurator', 'evenement.plugin'));
    }
}
