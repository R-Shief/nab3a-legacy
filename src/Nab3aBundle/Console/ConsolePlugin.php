<?php

namespace Nab3aBundle\Console;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ConsolePlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'console';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('console.yml');
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddConsoleCommandPass());
    }
}
