<?php

namespace Nab3aBundle\Stream;

use Evenement\EventEmitterInterface;
use Nab3aBundle\Evenement\PluginInterface;
use Psr\Log\LoggerAwareTrait;
use React\EEP\Stats\Count;
use React\EEP\Stats\Max;
use React\EEP\Stats\Mean;
use React\EEP\Window;
use React\EventLoop\LoopInterface;

class Eep implements PluginInterface
{
    use LoggerAwareTrait;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param EventEmitterInterface $emitter
     *
     * @return mixed
     */
    public function attachEvents(EventEmitterInterface $emitter)
    {
        $cnt_tw = new Window\Periodic(new Count(), 6e4);
        $max_tw = new Window\Periodic(new Max(), 6e4);
        $mean_tw = new Window\Periodic(new Mean(), 6e4);

        $cnt_tw->on('emit', function ($emit) {
            $this->logger->alert('statusCount '.$emit);
        });

        $max_tw->on('emit', function ($emit) {
            $this->logger->alert('maxIdlePeriod '.round($emit, 2));
        });

        $mean_tw->on('emit', function ($emit) {
            $this->logger->alert('avgElapsed '.round($emit, 2));
        });

        $emitter->on('tweet', function () use ($cnt_tw, $max_tw, $mean_tw) {
            static $time = 0;

            $cnt_tw->enqueue(1);

            $value = microtime(true) - $time;
            $time = microtime(true);
            $mean_tw->enqueue($value);
            $max_tw->enqueue($value);
        });

        $this->loop->addPeriodicTimer(60, function () use ($cnt_tw, $max_tw, $mean_tw) {
            $cnt_tw->tick();
            $max_tw->tick();
            $mean_tw->tick();
        });
    }
}
