<?php

namespace Nab3aBundle\Tests\Stream;

use Clue\JsonStream\StreamingJsonParser;
use GuzzleHttp\Psr7\PumpStream;
use Nab3aBundle\Stream\TwitterStream;

class TwitterStreamTest extends \PHPUnit_Framework_TestCase
{
    const TWITTER_EOL = "\r\n";

    public function testHandleData()
    {
        $data = [
          '{"a":"b"}',
          '{"c":"d"}',
          '{"e":[]}',
        ];
        $string = array_reduce($data, function ($carry, $item) {
            return $carry
                .strlen($item.self::TWITTER_EOL)
                .self::TWITTER_EOL
                .$item
                .self::TWITTER_EOL;
        }, '');
        $length = array_reduce($data, function ($carry, $item) {
            return $carry + strlen($item.self::TWITTER_EOL);
        }, 0);

        $stream = \GuzzleHttp\Psr7\stream_for($string);
        $fn = new PumpStream(function ($length) use ($stream) {
            return TwitterStream::handleData($stream, $length);
        });

        $data = $fn->read($length);
        $stream = new StreamingJsonParser();
        $objects = $stream->push($data);

        $this->assertCount(3, $objects);
    }
}
