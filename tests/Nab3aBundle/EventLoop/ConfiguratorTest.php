<?php

namespace Nab3aBundle\Tests\EventLoop;

use Nab3aBundle\EventLoop\Configurator;
use Nab3aBundle\EventLoop\PluginInterface;
use React\EventLoop\LoopInterface;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $loop = $this->getMockBuilder(LoopInterface::class)->getMock();
        $loop->expects($this->once())->method('nextTick')->with(function (LoopInterface $loop) {});

        $plugin = new Plugin();
        $configurator = new Configurator([$plugin]);
        $configurator->configure($loop);
    }
}

class Plugin implements PluginInterface
{
    public function attach(LoopInterface $loop)
    {
        $loop->nextTick(function (LoopInterface $loop) {});
    }
}
