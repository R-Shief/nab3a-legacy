<?php

namespace Nab3aBundle\Tests\Console;

use Monolog\Handler\TestHandler;
use Nab3aBundle\Console\LoggerHelper;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoggerHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        $container = new ContainerBuilder();
        $container->register('monolog.logger', Logger::class)->setArguments(['app']);
        $container->register('monolog.logger.other', Logger::class)->setArguments(['other']);
        $container->compile();
        $this->container = $container;
    }

    public function testOnData()
    {
        $output = new StreamOutput(fopen('php://memory', 'r+'));
        $helper = new LoggerHelper($output);
        $helper->setContainer($this->container);

        $record = array(
          'message' => 'LOG',
          'context' => array(),
          'level' => Logger::INFO,
          'level_name' => LogLevel::INFO,
          'channel' => 'app',
          'datetime' => new \DateTime(),
          'extra' => array(),
        );

        $handler = new TestHandler();
        /** @var Logger $logger */
        $logger = $this->container->get('monolog.logger');
        $logger->setHandlers([$handler]);
        $helper->onData(json_encode($record));
        $messages = $handler->getRecords();
        $this->assertTrue($handler->hasRecordThatContains('LOG', Logger::INFO));
        $this->assertContains('LOG', $messages[0]['message']);

        $handler = new TestHandler();
        $record['channel'] = 'other';
        $logger = $this->container->get('monolog.logger.other');
        $logger->setHandlers([$handler]);
        $helper->onData(json_encode($record));
        $messages = $handler->getRecords();
        $this->assertTrue($handler->hasRecordThatContains('LOG', Logger::INFO));
        $this->assertContains('LOG', $messages[0]['message']);

        $helper->onData('OUTPUT');
        fseek($output->getStream(), 0);
        $string = fread($output->getStream(), 6);
        $this->assertContains('OUTPUT', $string);
    }
}
