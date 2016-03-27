<?php

namespace Nab3aBundle\Debug;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class DebugPlugin extends BundlePlugin
{
    public function build(ContainerBuilder $container)
    {
        $container->setDefinition('nab3a.event_loop', new DefinitionDecorator('nab3a.event_loop.debug'));
    }
}
