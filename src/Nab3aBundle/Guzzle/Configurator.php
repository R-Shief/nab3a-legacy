<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class Configurator
{
    /**
     * @var array
     */
    private $plugins;

    public function __construct(array $plugins = array())
    {
        $this->plugins = $plugins;
    }

    public static function createHandler(callable $handler = null)
    {
        $handler = $handler ?: \GuzzleHttp\choose_handler();

        return $handler;
    }

    public function __invoke(HandlerStack $stack)
    {
        $stack->push(Middleware::httpErrors(), 'http_errors');
        $stack->push(Middleware::prepareBody(), 'prepare_body');
        array_walk($this->plugins, function (MiddlewareInterface $plugin) use ($stack) {
            $plugin->push($stack);
        });
    }
}
