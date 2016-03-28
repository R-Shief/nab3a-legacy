<?php

namespace Nab3aBundle\Debug;

use GuzzleHttp\Promise\TaskQueue;
use Nab3aBundle\EventLoop\PluginInterface;
use Psr\Log\LoggerAwareTrait;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use WyriHaximus\React\Inspector\InfoProvider;

class LoopPlugin implements PluginInterface
{
    use LoggerAwareTrait;

    /**
     * @var InfoProvider
     */
    private $infoProvider;

    /**
     * @var TaskQueue
     */
    private $queue;

    public function __construct(InfoProvider $infoProvider, TaskQueue $queue)
    {
        $this->infoProvider = $infoProvider;
        $this->queue = $queue;
    }

    public function attach(LoopInterface $loop)
    {
        $loop->addPeriodicTimer(1, [$this, 'logCounters']);
    }

    public function logCounters(TimerInterface $timer)
    {
        $counters = $this->infoProvider->getCounters();
        foreach ($counters as $entity => $value) {
            foreach ($value as $metric => $stats) {
                $show = count(array_filter($stats));
                $message = $entity.' '.$metric;
                if ($show) {
                    $context = $stats;
                    $this->logger->notice($message, $context);
                } else {
                    $this->logger->debug('no '.$message);
                }
            }
        }
    }
}
