<?php

namespace Nab3aBundle\Tests\Evenement;

use Evenement\EventEmitter;
use Evenement\EventEmitterInterface;
use Nab3aBundle\Evenement\Configurator;
use Nab3aBundle\Evenement\PluginInterface;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $emitter = new EventEmitter();
        $plugin = new Plugin();
        $configurator = new Configurator([$plugin]);
        $configurator->configure($emitter);

        $emitter->emit('event', [$this]);
    }
}

class Plugin implements PluginInterface
{
    public function attachEvents(EventEmitterInterface $emitter)
    {
        $emitter->on('event', function (\PHPUnit_Framework_TestCase $testCase) {
            $testCase->assertTrue(true);
        });
    }
}
