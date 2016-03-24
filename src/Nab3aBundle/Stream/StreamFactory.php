<?php

namespace Nab3aBundle\Stream;

use Psr\Http\Message\MessageInterface;
use React\EventLoop\LoopInterface;

class StreamFactory
{
    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function fromMessage(MessageInterface $message)
    {
        $stream = $message->getBody();

        return new TwitterStream($stream, $this->loop);
    }
}
