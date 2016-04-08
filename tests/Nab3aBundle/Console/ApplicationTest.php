<?php

namespace Nab3aBundle\Tests\Console;

use Nab3aBundle\Console\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDefaultInputDefinition()
    {
        $app = new Application();
        $definition = $app->getDefinition();
        $this->assertTrue($definition->hasOption('child'));
    }
}
