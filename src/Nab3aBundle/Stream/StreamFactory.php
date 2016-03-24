<?php

namespace Nab3aBundle\Stream;

use Psr\Http\Message\MessageInterface;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;

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

    public function fromFilePath($path)
    {
        return new Stream(fopen($path, 'w'), $this->loop);
    }
}
