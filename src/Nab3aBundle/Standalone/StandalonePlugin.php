<?php

namespace Nab3aBundle\Standalone;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class StandalonePlugin extends BundlePlugin implements PrependExtensionInterface
{
    /**
     * @var \Nab3aBundle\Loader\YamlFileLoader
     */
    private $loader;
    private $locator;

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
