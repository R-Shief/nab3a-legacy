<?php

namespace Nab3aBundle\Evenement;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Nab3aBundle\DependencyInjection\Compiler\AttachPluginsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EvenementPlugin extends BundlePlugin
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AttachPluginsCompilerPass('nab3a.evenement.configurator', 'evenement.plugin'));
    }
}
