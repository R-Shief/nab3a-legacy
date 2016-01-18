<?php

namespace AppBundle\StreamParameter;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\Config\Loader\LoaderInterface;

class Watcher implements EventEmitterInterface
{
    use EventEmitterTrait;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * FilterParameterWatcher constructor.
     *
     * @param LoopInterface   $loop
     * @param LoaderInterface $loader
     */
    public function __construct(LoopInterface $loop, LoaderInterface $loader)
    {
        $this->loader = $loader;
        $this->loop = $loop;
    }

    /**
     * @param $resource
     * @param int $interval
     *
     * @return TimerInterface
     */
    public function watch($resource, $interval = 15)
    {
        // The listener notices changes in the streaming filter parameters.
        $function = $this->listenerFactory($resource);

        // Load the parameters at startup.
        $this->loop->nextTick($function);

        // Also schedule them to be watched.
        return $this->loop->addPeriodicTimer($interval, $function);
    }

    /**
     * Returns a listener that notices when a resource changes.
     *
     * @param $resource mixed anything that can be loaded
     *
     * @return \Closure
     */
    private function listenerFactory($resource)
    {
        /* @var array $current */
        return function () use ($resource, &$current) {
            $params = $this->loader->load($resource);
            if (!$current || $current !== $params) {
                $current = $params;
                $this->emit('filter_change', [$params]);
            }
        };
    }
}
