<?php

namespace Nab3aBundle\Stream;

use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableStream;

class TwitterStream extends ReadableStream
{
    /**
     * @var bool
     */
    private $pause;

    /**
     * @var StreamInterface
     */
    private $stream;

    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(StreamInterface $stream, LoopInterface $loop)
    {
        $this->stream = $stream;
        $this->loop = $loop;
        $this->resume();
    }

    /**
     * Read one entry from a length delimited stream.
     *
     * @param StreamInterface $stream
     *
     * @return string
     */
    public static function handleData(StreamInterface $stream)
    {
        do {
            $data = Psr7\readline($stream);
        } while (strlen($data) < 1);

        $length = intval(trim($data));

        if ($length) {
            $data = $stream->read($length);
        }

        return $data;
    }

    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    public function pause()
    {
        $this->pause = true;
    }

    public function resume()
    {
        if ($this->stream->isReadable()) {
            $this->pause = false;
            $this->loop->futureTick($this->futureTickListener($this->stream));
        }
    }

    public function close()
    {
        parent::close();
        $this->stream->close();
    }

    private function futureTickListener(StreamInterface $stream)
    {
        return function (LoopInterface $loop) use ($stream) {
            $data = self::handleData($stream);
            $this->emit('data', array($data));
            if (!$this->pause) {
                $loop->futureTick($this->futureTickListener($stream));
            }
        };
    }
}
