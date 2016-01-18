<?php

namespace AppBundle\Stream;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\MessageInterface;
use React\EventLoop\LoopInterface;

class StreamFactory
{
    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function stream(PromiseInterface $promise)
    {
        return $promise->then(function (MessageInterface $message) {
            $stream = $message->getBody();

            return new TwitterStream($stream, $this->loop);
        });
    }
}
