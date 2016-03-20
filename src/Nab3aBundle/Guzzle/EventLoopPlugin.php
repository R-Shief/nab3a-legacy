<?php

namespace Nab3aBundle\Guzzle;

use Nab3aBundle\EventLoop\PerpetualListener;
use Nab3aBundle\EventLoop\PluginInterface;
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
