<?php

namespace Nab3aBundle\Guzzle;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GuzzlePlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'guzzle';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('guzzle.yml');
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new StackMiddlewareCompilerPass());
    }
}
