<?php

namespace Nab3aBundle\Tests\Debug\Compiler;

use Nab3aBundle\Debug\Compiler\DebugEventLoopCompilerPass;
use React\EventLoop\StreamSelectLoop;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WyriHaximus\React\Inspector\LoopDecorator;

class DebugEventLoopCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testCompile()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new DebugEventLoopCompilerPass());
        $container->register('nab3a.event_loop', StreamSelectLoop::class);
        $container->register('nab3a.event_loop.debug', LoopDecorator::class);
        $container->compile();

        $definition = $container->findDefinition('nab3a.event_loop');
        $this->assertEquals(LoopDecorator::class, $definition->getClass());
    }
}
