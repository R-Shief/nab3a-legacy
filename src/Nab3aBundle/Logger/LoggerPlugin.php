<?php

namespace Nab3aBundle\Logger;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class LoggerPlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'logger';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('logger.yml');
    }
}
