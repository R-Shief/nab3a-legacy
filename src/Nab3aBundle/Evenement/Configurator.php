<?php

namespace Nab3aBundle\Evenement;

use Evenement\EventEmitterInterface;

class Configurator
{
    /**
     * @var array
     */
    private $plugins;

    public function __construct(array $plugins = array())
    {
        $this->plugins = $plugins;
    }

    public function configure(EventEmitterInterface $emitter)
    {
        array_walk($this->plugins, function (PluginInterface $plugin) use ($emitter) {
            $plugin->attachEvents($emitter);
        });
    }
}
