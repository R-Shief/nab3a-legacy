<?php

namespace Nab3aBundle\EventLoop;

use React\EventLoop\LoopInterface;

class PerpetualListener
{
    public static function wrap(callable $callback)
    {
        $listener = function (LoopInterface $loop) use ($callback, &$listener) {
            $callback($loop);
            $loop->futureTick($listener);
        };

        return $listener;
    }
}
