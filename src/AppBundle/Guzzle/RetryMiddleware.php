<?php

namespace AppBundle\Guzzle;

use GuzzleHttp\Exception;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerAwareTrait;

class RetryMiddleware
{
    use LoggerAwareTrait;

    /**
     * @var callable
     */
    private $nextHandler;

    public function __construct(callable $nextHandler)
    {
        $this->nextHandler = $nextHandler;
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        $prev = $this->nextHandler;

        foreach (array_reverse(self::retryMiddlewares()) as $fn) {
            $prev = $fn[0]($prev);
        }

        return $prev($request, $options);
    }

    private static function retryMiddlewares()
    {
        return array(
          [Middleware::retry(self::rateLimitErrorDecider(), self::exponentialDelay(60000)), 'rate_limit'],
          [Middleware::retry(self::httpErrorDecider(), self::exponentialDelay(5000, 320000)), 'http_error'],
          [Middleware::retry(self::connectExceptionDecider(), self::linearDelay(250, 16000)), 'connect_error'],
        );
    }

    public static function connectExceptionDecider()
    {
        return function ($retries, Psr7\Request $request, Psr7\Response $response = null, Exception\RequestException $exception = null) {
            return $exception instanceof Exception\ConnectException;
        };
    }

    public static function rateLimitErrorDecider()
    {
        return function ($retries, Psr7\Request $request, Psr7\Response $response = null, Exception\RequestException $exception = null) {
            return $exception instanceof Exception\ClientException && $exception->hasResponse() && $exception->getResponse()->getStatusCode() === 420;
        };
    }

    public static function httpErrorDecider()
    {
        return function ($retries, Psr7\Request $request, Psr7\Response $response = null, Exception\RequestException $exception = null) {
            return $exception instanceof Exception\BadResponseException;
        };
    }

    public static function exponentialDelay($base, $maxDelay = 0)
    {
        return function ($retries) use ($base, $maxDelay) {
            $delay = \GuzzleHttp\RetryMiddleware::exponentialDelay($retries) * $base;

            return $maxDelay ? min($delay, $maxDelay) : $delay;
        };
    }

    public static function linearDelay($base, $maxDelay = 0)
    {
        return function ($retries) use ($base, $maxDelay) {
            $delay = $retries * $base;

            return $maxDelay ? min($delay, $maxDelay) : $delay;
        };
    }
}
