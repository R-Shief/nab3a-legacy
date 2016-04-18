<?php

namespace Nab3aBundle\EventLoop;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Nab3aBundle\DependencyInjection\Compiler\AttachPluginsCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventLoopPlugin extends BundlePlugin
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AttachPluginsCompilerPass(Configurator::class, 'event_loop.plugin', 'nab3a.event_loop'), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
