<?php

namespace Nab3aBundle\Standalone;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerDebugDumpPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        echo implode(PHP_EOL, $container->getCompiler()->getLog()).PHP_EOL;
    }
}
