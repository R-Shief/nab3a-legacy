<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use React\EventLoop\LoopInterface;

class Configurator
{
    /**
     * @var array
     */
    private $plugins;
    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop, array $plugins = array())
    {
        $this->plugins = $plugins;
        $this->loop = $loop;
    }

    public function create(callable $handler = null)
    {
        $handler = $handler ?: \GuzzleHttp\choose_handler();

        $tap = Middleware::tap(function ($request, $options) {
        }, function ($request, $options, $response) {
        });

        return new HandlerStack($handler);
    }

    public function configure(HandlerStack $stack)
    {
        $stack->push(Middleware::httpErrors(), 'http_errors');
        $stack->push(Middleware::prepareBody(), 'prepare_body');
        array_walk($this->plugins, function (MiddlewareInterface $plugin) use ($stack) {
            $plugin->push($stack);
        });
    }
}
