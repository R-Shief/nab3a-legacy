<?php

namespace Nab3aBundle\Console;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsolePlugin extends BundlePlugin
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddConsoleCommandPass());
    }
}
