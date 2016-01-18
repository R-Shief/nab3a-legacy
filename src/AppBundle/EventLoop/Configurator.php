<?php

namespace AppBundle\EventLoop;

use React\EventLoop\LoopInterface;

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

    public function configure(LoopInterface $loop)
    {
        array_walk($this->plugins, function (PluginInterface $plugin) use ($loop) {
            $plugin->attach($loop);
        });
    }
}
