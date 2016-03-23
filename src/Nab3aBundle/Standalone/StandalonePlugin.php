<?php

namespace Nab3aBundle\Standalone;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class StandalonePlugin extends BundlePlugin implements PrependExtensionInterface
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PruneServicesCompilerPass());
    }

    public function prepend(ContainerBuilder $container)
    {
    }
}
