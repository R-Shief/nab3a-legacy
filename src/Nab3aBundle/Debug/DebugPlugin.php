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
        // replace the regular event_dispatcher service with the debug one
        $definition = $container->findDefinition('nab3a.event_loop');
        $definition->setPublic(false);
        $container->setDefinition('nab3a.event_loop.parent', $definition);
        $container->setAlias('event_dispatcher', 'nab3a.event_loop.debug');
    }
}
