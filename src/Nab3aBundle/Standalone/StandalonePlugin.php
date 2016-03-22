<?php

namespace Nab3aBundle\Standalone;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Nab3aBundle\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class StandalonePlugin extends SimpleBundlePlugin implements PrependExtensionInterface
{
    /**
     * @var
     */
    private $name;

    /**
     * @var \Nab3aBundle\Loader\YamlFileLoader
     */
    private $loader;
    private $locator;

//    public function __construct()
//    {
//        $this->name = $name;
//        $paths = [$_SERVER['HOME'].'/.rshief', getcwd()];
//        $this->locator = new FileLocator($paths);
//        $this->loader = new YamlFileLoader($this->locator);
//    }

    public function name()
    {
        return 'standalone';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config']));
        $loader->load('standalone.yml');
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PruneServicesCompilerPass());
    }

    public function prepend(ContainerBuilder $container)
    {
    }

    public function boot(ContainerInterface $container)
    {
    }
}
