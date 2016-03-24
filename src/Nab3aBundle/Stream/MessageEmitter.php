<?php

namespace Nab3aBundle\Stream;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use Nab3aBundle\Evenement\PluginInterface;

/**
 * Class MessageEmitter.
 *
 * This emitter is also a listener because it converts raw messages
 * into typed messages.
 */
class MessageEmitter implements EventEmitterInterface, PluginInterface
{
    use EventEmitterTrait;

    /**
     * @var TypeGuesser
     */
    private $guesser;

    public function __construct(TypeGuesser $guesser)
    {
        $this->guesser = $guesser;
    }

    /**
     * @param EventEmitterInterface $emitter
     *
     * @return mixed
     */
    public function attachEvents(EventEmitterInterface $emitter)
    {
        $emitter->on('data', [$this, 'onData']);
    }

    /**
     * @param $data
     */
    public function onData($data)
    {
        $event = $this->guesser->getEventName($data);
        $this->emit($event, [$data]);
    }
}
