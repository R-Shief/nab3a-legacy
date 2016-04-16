<?php

namespace Nab3aBundle\Tests\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use Nab3aBundle\Guzzle\StackMiddlewareCompilerPass;
use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StackMiddlewareCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new StackMiddlewareCompilerPass());

        $container->register('guzzle.client', Client::class);
        $definition = $container->register('nab3a.guzzle.handler_stack', HandlerStack::class);
        $definition->setArguments([\GuzzleHttp\choose_handler()]);

        $definition = $container->register('something', \Closure::class);
        $definition->setFactory('GuzzleHttp\Middleware::log');
        $definition->setArguments([new BufferingLogger(), new MessageFormatter()]);
        $definition->addTag('guzzle.middleware', ['id' => 'guzzle.client', 'name' => 'log']);

        $container->compile();

        /** @var Client $client */
        $client = $container->get('guzzle.client');
        $config = $client->getConfig();
        /** @var HandlerStack $handler */
        $handler = $config['handler'];
        $this->assertContains('Name: \'log\'', (string) $handler);
    }
}
