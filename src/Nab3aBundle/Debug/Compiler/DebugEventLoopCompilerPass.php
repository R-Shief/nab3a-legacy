<?php

namespace Nab3aBundle\Debug\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DebugEventLoopCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // replace the regular event_dispatcher service with the debug one
        $definition = $container->findDefinition('nab3a.event_loop');
        $definition->setPublic(false);
        $container->setDefinition('nab3a.event_loop.parent', $definition);
        $container->setAlias('nab3a.event_loop', 'nab3a.event_loop.debug');

        $definition = $container->getDefinition('nab3a.event_loop.debug');
        $definition->setArguments([new Reference('nab3a.event_loop.parent')]);
    }
}
