<?php

namespace AppBundle\Guzzle;

use AppBundle\EventLoop\PerpetualListener;
use AppBundle\EventLoop\PluginInterface;
use React\EventLoop\LoopInterface;

class EventLoopPlugin implements PluginInterface
{
    public function attach(LoopInterface $loop)
    {
        $queue = \GuzzleHttp\Promise\queue();

        $listener = PerpetualListener::wrap([$queue, 'run']);
        $loop->futureTick($listener);
    }
}
