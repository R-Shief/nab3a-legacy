<?php

namespace Nab3aBundle\Guzzle;

use Evenement\EventEmitterTrait;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareTrait;

class Emitter
{
    use EventEmitterTrait;
    use LoggerAwareTrait;

    public function onHeaders(ResponseInterface $response) {
        // nothing.
    }

    public function onStats(TransferStats $stats)
    {
        $this->logger->info(sprintf('Transfer time: %f seconds', $stats->getTransferTime()), $stats->getHandlerStats());
    }
}
