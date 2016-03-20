<?php

namespace Nab3aBundle\Evenement;

use Evenement\EventEmitterInterface;

interface PluginInterface
{
    /**
     * @param EventEmitterInterface $emitter
     *
     * @return mixed
     */
    public function attachEvents(EventEmitterInterface $emitter);
}
