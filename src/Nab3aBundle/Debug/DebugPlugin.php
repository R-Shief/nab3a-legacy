<?php

namespace Nab3aBundle\Debug;

use Nab3aBundle\Debug\Compiler\DebugEventLoopCompilerPass;
use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DebugPlugin extends BundlePlugin
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DebugEventLoopCompilerPass());
    }
}
