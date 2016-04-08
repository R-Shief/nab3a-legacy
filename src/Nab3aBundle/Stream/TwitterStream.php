<?php

namespace Nab3aBundle\Stream;

use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;

class TwitterStream
{
    /**
     * Read one entry from a length delimited stream.
     *
     * @param StreamInterface $stream
     * @param null            $maxLength
     *
     * @return string
     */
    public static function handleData(StreamInterface $stream, $maxLength = null)
    {
        do {
            $data = Psr7\readline($stream, $maxLength);
        } while (strlen($data) < 1);

        $length = intval(trim($data));

        if ($length) {
            $data = $stream->read($length);
        }

        return $data;
    }
}
