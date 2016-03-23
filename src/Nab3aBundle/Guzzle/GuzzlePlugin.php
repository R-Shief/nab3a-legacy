<?php

namespace Nab3aBundle\Guzzle;

use Nab3aBundle\DependencyInjection\BundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GuzzlePlugin extends BundlePlugin
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new StackMiddlewareCompilerPass());
    }
}
