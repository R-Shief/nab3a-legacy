<?php

namespace Nab3aBundle\Debug;

use GuzzleHttp\Promise\TaskQueue;
use Nab3aBundle\EventLoop\PluginInterface;
use PHPPM\Utils;
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
        $timer = $loop->addPeriodicTimer(1, function () {
        });
        Utils::bindAndCall(function () {
            dump($this->queue);
        }, $this->queue, [], []);

        $timer = $loop->addPeriodicTimer(1, [$this, 'info']);
    }

    public function info(TimerInterface $timer)
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
