<?php

namespace AppBundle\StreamMessage;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use GuzzleHttp\Promise\PromiseInterface;
use React\Stream\ReadableStreamInterface;

class Emitter implements EventEmitterInterface
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

    public function messages(PromiseInterface $promise)
    {
        return $promise->then(function (ReadableStreamInterface $stream) {
            $stream->on('data', function ($data) {
                $event = $this->guesser->getEventName($data);
                $this->emit($event, [$data]);
            });

            return $stream;
        });
    }
}
