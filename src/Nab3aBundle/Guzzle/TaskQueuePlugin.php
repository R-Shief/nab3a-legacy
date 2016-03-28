<?php

namespace Nab3aBundle\Guzzle;

use Nab3aBundle\EventLoop\PluginInterface;
use React\EventLoop\LoopInterface;

class TaskQueuePlugin implements PluginInterface
{
    /**
     * @var callable
     */
    private $listener;

    /**
     * TaskQueuePlugin constructor.
     *
     * @param callable $listener
     */
    public function __construct(callable $listener)
    {
        $this->listener = $listener;
    }

    public function attach(LoopInterface $loop)
    {
        $loop->futureTick($this->listener);
    }
}
