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

        $template = 'retry {retry} delay {delay}';
//        $stack->after('retry', self::logRetry($this->logger, $template, $this->logLevel), 'log.retry');
    }

    public static function logRetry(LoggerInterface $logger, $template, $logLevel = LogLevel::INFO)
    {
        // Grab the client's handler instance.
        $clientHandler = $client->getConfig('handler');
// Create a middleware that echoes parts of the request.
        $tapMiddleware = Middleware::tap(function ($request) {
            echo $request->getHeader('Content-Type');
            // application/json
            echo $request->getBody();
            // {"foo":"bar"}
        });

        $response = $client->request('PUT', '/put', [
          'json' => ['foo' => 'bar'],
          'handler' => $tapMiddleware($clientHandler),
        ]);
    }
}
