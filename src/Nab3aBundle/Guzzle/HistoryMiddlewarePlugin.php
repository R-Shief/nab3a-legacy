<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class HistoryMiddlewarePlugin implements MiddlewareInterface
{
    /**
     * @var array
     */
    private $container = array();

    public function push(HandlerStack $stack)
    {
        $stack->push(Middleware::history($this->container), 'history');
    }

    public function container()
    {
        return $this->container;
    }
}
