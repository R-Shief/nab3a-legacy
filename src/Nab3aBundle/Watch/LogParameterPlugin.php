<?php

namespace Nab3aBundle\Watch;

use Nab3aBundle\Evenement\PluginInterface;
use Evenement\EventEmitterInterface;
use Psr\Log\LoggerAwareTrait;

class LogParameterPlugin implements PluginInterface
{
    use LoggerAwareTrait;

    public function attachEvents(EventEmitterInterface $emitter)
    {
        $emitter->on('filter_change', function ($params) {
            // Check if filter is ready + allowed to be updated (reconnect)
            $this->logger->info('filter_change', $params);
        });
    }
}
