<?php

namespace Nab3aBundle\Stream;

use Evenement\EventEmitterInterface;
use Nab3aBundle\Evenement\PluginInterface;
use Psr\Log\LoggerAwareTrait;
use React\EEP\Composite;
use React\EEP\Stats;
use React\EEP\Window;
use React\EventLoop\LoopInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Eep implements PluginInterface
{
    use LoggerAwareTrait;
    use ContainerAwareTrait;

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
        $emitter->on('tweet', [$this, 'tweetTimer']);
        $emitter->on('tweet', [$this, 'tweetCounter']);

        $this->loop->addPeriodicTimer(.5, [$this, 'ticker']);
    }

    /**
     *
     */
    public function tweetTimer()
    {
        static $time;

        if ($time) {
            $ut = microtime(true);
            $v = abs($ut - $time);
            $this->container->get('nab3a.stream.eep.idle_time')->enqueue($v);
            $time = $ut;
        } else {
            $time = microtime(true);
        }
    }

    /**
     * @param $data
     */
    public function tweetCounter($data)
    {
        $this->container->get('nab3a.stream.eep.status_counter')->enqueue($data);
        $this->container->get('nab3a.stream.eep.status_averager')->enqueue($data);
    }

    /**
     *
     */
    public function ticker()
    {
        $this->container->get('nab3a.stream.eep.status_counter')->tick();
        $this->container->get('nab3a.stream.eep.status_averager')->tick();
        $this->container->get('nab3a.stream.eep.idle_time')->tick();
    }

    /**
     * @return \React\EEP\Window\Periodic
     */
    public function makeIdleTimeTracker()
    {
        $idle_p = new Window\Periodic(new Composite([
          new Stats\Max(), new Stats\Mean(), new Stats\Min(), new Stats\Sum(),
        ]), 6e4);
        $idle_p->on('emit', function ($emit) {
            $context = array_combine(['max', 'mean', 'min', 'total'], array_map(function ($num) {
                return round($num, 4);
            }, $emit));
            $this->logger->info('idleTime', $context);
        });

        return $idle_p;
    }

    /**
     * @return \React\EEP\Window\Periodic
     */
    public function makeStatusCounter()
    {
        $cnt_tw = new Window\Periodic(new Stats\Count(), 6e4);
        $cnt_tw->on('emit', function ($emit) {
            $this->logger->info('statusCount '.$emit.' in one minute');
        });

        return $cnt_tw;
    }

    /**
     * @return \React\EEP\Window\Periodic
     */
    public function makeStatusAverager()
    {
        $avg_tw = new Window\Sliding(new Stats\Mean(), 60);
        $avg_tw->on('emit', function ($emit) {
            $this->logger->info('statusAverage '.$emit.' per second');
        });

        $cnt_tw = new Window\Periodic(new Stats\Count(), 1e3);
        $cnt_tw->on('emit', function ($emit) use ($avg_tw) {
            $avg_tw->enqueue($emit);
        });

        return $cnt_tw;
    }
}
