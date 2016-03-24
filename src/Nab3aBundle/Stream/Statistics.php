<?php

namespace Nab3aBundle\Stream;

use Psr\Log\LoggerAwareTrait;

class Statistics
{
    use LoggerAwareTrait;

    /**
     * Seconds since the last call to statusUpdate().
     *
     * Reset to zero after each call to statusUpdate()
     * Highest value it should ever reach is $this->avgPeriod
     *
     * @var int
     */
    private $avgElapsed;
    private $avgPeriod = 60;

    /**
     * Total number of seconds (fractional) spent in the enqueueStatus() calls (i.e. the customized
     * function that handles each received tweet).
     *
     * @var float
     */
    private $enqueueSpent;

    /**
     * Time spent on each call to enqueueStatus() (i.e. average time spent, in milliseconds,
     * spent processing received tweet).
     *
     * Simply: enqueueSpent divided by statusCount
     * Note: by default, calculated fresh for past 60 seconds, every 60 seconds.
     *
     * @var float
     */
    private $enqueueTimeMS;

    /**
     * The number of calls to $this->checkFilterPredicates().
     *
     * By default it is called every 5 seconds, so if doing statusUpdates every
     * 60 seconds and then resetting it, this will usually be 12.
     *
     * @var int
     */
    private $filterCheckCount;

    /**
     * Total number of seconds (fractional) spent in the checkFilterPredicates() calls.
     *
     * @var float
     */
    private $filterCheckSpent;

    /**
     * Like $enqueueTimeMS but for the checkFilterPredicates() function.
     *
     * @var float
     */
    private $filterCheckTimeMS;

    /**
     * Number of seconds since the last tweet arrived (or the keep-alive newline).
     *
     * @var int
     */
    private $idlePeriod;

    /**
     * The maximum value $this->idlePeriod has reached.
     *
     * @var int
     */
    private $maxIdlePeriod;

    /**
     * Number of tweets received.
     *
     * Note: by default this is the sum for last 60 seconds, and is therefore
     * reset every 60 seconds.
     * To change this behaviour write a custom statusUpdate() function.
     *
     * @var int
     */
    private $statusCount;

    /**
     * The number of tweets received per second in previous minute; calculated fresh
     * just before each call to statusUpdate()
     * I.e. if fewer than 30 tweets in last minute then this will be zero; if 30 to 90 then it
     * will be 1, if 90 to 150 then 2, etc.
     *
     * @var int
     */
    private $statusRate;

    protected $idleReconnectTimeout = 90;

    public function idleTimeout()
    {
        /* Unfortunately, we need to do a safety check for dead twitter streams - This seems to be able to happen where
         * you end up with a valid connection, but NO tweets coming along the wire (or keep alives). The below guards
         * against this.
         */
        if ((time() - $lastStreamActivity) > $this->idleReconnectTimeout) {
            $this->logger->info('Idle timeout: No stream activity for > '.$this->idleReconnectTimeout.' seconds. '.
                ' Reconnecting.');
            $this->reconnect();
            $lastStreamActivity = time();
        }
    }

    public function getMessage()
    {
        // Init state
        $lastAverage = $lastFilterCheck = $lastFilterUpd = $lastStreamActivity = time();

        // Calc counter averages
        $this->avgElapsed = time() - $lastAverage;
        if ($this->avgElapsed >= $this->avgPeriod) {
            // Calc tweets-per-second
            $this->statusRate = round($this->statusCount / $this->avgElapsed, 0);

            // Calc time spent per enqueue in ms
            $this->enqueueTimeMS = ($this->statusCount > 0) ? round($this->enqueueSpent / $this->statusCount * 1000, 2) : 0;

            // Calc time spent total in filter predicate checking
            $this->filterCheckTimeMS = ($this->filterCheckCount > 0) ? round($this->filterCheckSpent / $this->filterCheckCount * 1000, 2) : 0;

            $lastAverage = time();
        }

        if (function_exists('pcntl_signal_dispatch')) {
            pcntl_signal_dispatch();
        }

        $this->logger->info('Consume rate: '.$this->statusRate.' status/sec ('.$this->statusCount.' total), avg '.
            'enqueueStatus(): '.$this->enqueueTimeMS.'ms, avg checkFilterPredicates(): '.$this->filterCheckTimeMS.'ms ('.
            $this->filterCheckCount.' total) over '.$this->avgElapsed.' seconds, max stream idle period: '.
            $this->maxIdlePeriod.' seconds.');

        // Reset
        $this->statusCount = $this->filterCheckCount = $this->enqueueSpent = 0;
        $this->filterCheckSpent = $this->idlePeriod = $this->maxIdlePeriod = 0;
    }
}
