<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggingMiddlewarePlugin implements MiddlewareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $template;
    /**
     * @var string
     */
    private $logLevel;

    public function __construct($template = MessageFormatter::CLF, $logLevel = LogLevel::INFO)
    {
        $this->template = $template;
        $this->logLevel = $logLevel;
    }

    public function push(HandlerStack $stack)
    {
        $stack->push(Middleware::log($this->logger, new MessageFormatter($this->template), $this->logLevel), 'log');
    }
}
