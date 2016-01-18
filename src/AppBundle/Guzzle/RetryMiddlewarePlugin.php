<?php

namespace AppBundle\Guzzle;

use GuzzleHttp\HandlerStack;

class RetryMiddlewarePlugin implements MiddlewareInterface
{
    public function push(HandlerStack $stack)
    {
        $stack->before('http_errors', function (callable $handler) {
           return new RetryMiddleware($handler);
        }, 'retry');
    }
}
