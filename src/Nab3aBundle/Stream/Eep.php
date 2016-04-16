<?php

namespace Nab3aBundle\Stream;

use Evenement\EventEmitterInterface;
use Nab3aBundle\Evenement\PluginInterface;
use Psr\Log\LoggerAwareTrait;
use React\EEP\Composite;
use React\EEP\Stats\Count;
use React\EEP\Stats\Max;
use React\EEP\Stats\Mean;
use React\EEP\Stats\Min;
use React\EEP\Stats\Sum;
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
        $emitter->on('tweet', function () {
            static $time;

            if ($time) {
                $ut = microtime(true);
                $v = abs($ut - $time);
                $this->container->get('nab3a.stream.eep.idle_time')->enqueue($v);
                $time = $ut;
            }
            else {
                $time = microtime(true);
            }
        });
        $emitter->on('tweet', function ($data) {
            $this->container->get('nab3a.stream.eep.status_counter')->enqueue($data);
        });

        $this->loop->addPeriodicTimer(.5, function () {
            $this->container->get('nab3a.stream.eep.status_counter')->tick();
            $this->container->get('nab3a.stream.eep.idle_time')->tick();
        });
    }

    public function makeIdleTimeTracker()
    {
        $idle_p = new Window\Periodic(new Composite([
          new Max(),
          new Mean(),
          new Min(),
          new Sum(),
        ]), 6e4);
        $idle_p->on('emit', function ($emit) {
            $context = array_combine(['max', 'mean', 'min', 'total'], array_map(function ($num) {
                return round($num, 4);
            }, $emit));
            $this->logger->info('idleTime', $context);
        });

        return $idle_p;
    }

    public function makeStatusCounter()
    {
        $cnt_tw = new Window\Periodic(new Count(), 6e4);
        $cnt_tw->on('emit', function ($emit) {
            $this->logger->info('statusCount '.$emit .' in one minute');
        });

        return $cnt_tw;
    }
}
