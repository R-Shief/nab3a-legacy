<?php

namespace Nab3aBundle\Stream;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use Nab3aBundle\Evenement\PluginInterface;

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

    public function onData($data)
    {
        $event = $this->guesser->getEventName($data);
        $this->emit($event, [$data]);
    }
}
