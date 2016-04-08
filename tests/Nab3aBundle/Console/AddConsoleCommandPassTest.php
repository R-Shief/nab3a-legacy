<?php

namespace Nab3aBundle\Tests\Console;

use Nab3aBundle\Console\AddConsoleCommandPass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AddConsoleCommandPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddConsoleCommandPass());
        $container->setParameter('my-command.class', 'Nab3aBundle\Tests\Console\MyCommand');

        $definition = new Definition('%my-command.class%');
        $definition->addTag('nab3a.console.command');
        $container->setDefinition('my-command', $definition);

        $definition = new Definition('Nab3aBundle\Console\Application');
        $container->setDefinition('nab3a.console.application', $definition);

        $container->compile();

        $this->assertTrue($definition->hasMethodCall('add'), 'Command is added to the application');
        $calls = $definition->getMethodCalls();
        foreach ($calls as $call) {
            if ($call[0] === 'add') {
                $this->assertEquals('my-command', (string) $call[1][0]);
            }
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The service "my-command" tagged "nab3a.console.command" must be public.
     */
    public function testProcessThrowAnExceptionIfTheServiceIsNotPublic()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddConsoleCommandPass());

        $definition = new Definition('Nab3aBundle\Tests\Console\MyCommand');
        $definition->addTag('nab3a.console.command');
        $definition->setPublic(false);
        $container->setDefinition('my-command', $definition);

        $container->compile();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The service "my-command" tagged "nab3a.console.command" must not be abstract.
     */
    public function testProcessThrowAnExceptionIfTheServiceIsAbstract()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddConsoleCommandPass());

        $definition = new Definition('Nab3aBundle\Tests\Console\MyCommand');
        $definition->addTag('nab3a.console.command');
        $definition->setAbstract(true);
        $container->setDefinition('my-command', $definition);

        $container->compile();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The service "my-command" tagged "nab3a.console.command" must be a subclass of "Symfony\Component\Console\Command\Command".
     */
    public function testProcessThrowAnExceptionIfTheServiceIsNotASubclassOfCommand()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddConsoleCommandPass());

        $definition = new Definition('SplObjectStorage');
        $definition->addTag('nab3a.console.command');
        $container->setDefinition('my-command', $definition);

        $container->compile();
    }
}

class MyCommand extends Command
{
}
