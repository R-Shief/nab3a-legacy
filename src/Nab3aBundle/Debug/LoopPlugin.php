<?php

namespace Nab3aBundle\Debug;

use Evenement\EventEmitterInterface;
use Nab3aBundle\EventLoop\PluginInterface;
use Psr\Log\LoggerAwareTrait;
use React\EventLoop\LoopInterface;
use WyriHaximus\React\Inspector\InfoProvider;

class LoopPlugin implements PluginInterface
{
    use LoggerAwareTrait;

    /**
     * @var InfoProvider
     */
    private $infoProvider;

    public function __construct(InfoProvider $infoProvider)
    {
        $this->infoProvider = $infoProvider;
    }

    public function attach(LoopInterface $loop)
    {
        if ($loop instanceof EventEmitterInterface) {
            $loop->on('addReadStream', function ($stream, $listener) {
                $this->logger->debug('addReadStream', stream_get_meta_data($stream));
            });
            $loop->on('addWriteStream', function ($stream, $listener) {
                $this->logger->debug('addWriteStream', stream_get_meta_data($stream));
            });
            $loop->on('removeReadStream', function ($stream) {
                $this->logger->debug('removeReadStream', stream_get_meta_data($stream));
            });
            $loop->on('removeWriteStream', function ($stream) {
                $this->logger->debug('removeWriteStream', stream_get_meta_data($stream));
            });
            $loop->on('removeStream', function ($stream) {
                $this->logger->debug('removeStream', stream_get_meta_data($stream));
            });
            $loop->on('addTimer', function ($interval, $callback, $loopTimer) {
                $this->logger->debug('addTimer', ['interval' => $interval]);
            });
            $loop->on('addPeriodicTimer', function ($interval, $callback, $loopTimer) {
                $this->logger->debug('addPeriodicTimer', ['interval' => $interval]);
            });

            $loop->on('runDone', function () {
                $this->logger->debug('runDone');
            });
            $loop->on('runStart', function () {
                $this->logger->debug('runStart');
            });
            $loop->on('stopDone', function () {
                $this->logger->debug('stopDone');
            });
            $loop->on('stopStart', function () {
                $this->logger->debug('stopStart');
            });
            $loop->on('tickDone', function () {
                $this->logger->debug('tickDone');
            });
            $loop->on('tickStart', function () {
                $this->logger->debug('tickStart');
            });
        }
        $loop->addPeriodicTimer(1, [$this, 'logCounters']);
    }

    public function logCounters()
    {
        $counters = $this->infoProvider->getCounters();
        foreach ($counters as $entity => $value) {
            foreach ($value as $metric => $stats) {
                $show = count(array_filter($stats));
                $message = $entity.' '.$metric;
                if ($show) {
                    $context = $stats;
                    $this->logger->debug($message, $context);
                } else {
                    $this->logger->debug('no '.$message);
                }
            }
        }
    }
}
