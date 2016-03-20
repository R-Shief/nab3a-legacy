<?php

namespace Nab3aBundle\Guzzle;

use Evenement\EventEmitterTrait;
use GuzzleHttp\TransferStats;
use Psr\Log\LoggerAwareTrait;

class Emitter
{
    use EventEmitterTrait;
    use LoggerAwareTrait;

    public function __invoke(TransferStats $stats)
    {
        $this->emit('stats', [$stats]);
        $this->logger->error('stats', ['transfer_time' => $stats->getTransferTime()]);
    }
}
