<?php

namespace Nab3aBundle\Core;

use Bangpound\Symfony\DependencyInjection\CallableCompilerPass;
use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class CorePlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'core';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('services.yml');
        $loader->load('guzzle.yml');
        $loader->load('console.yml');
    }

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
}
