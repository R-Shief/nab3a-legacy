<?php

namespace Nab3aBundle\Tests\Process;

use Clue\JsonStream\StreamingJsonParser;
use GuzzleHttp\Psr7\Stream;
use transducers as t;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testFlattenTweet()
    {
        $resource = fopen(__DIR__.'/../../tweets.json', 'r');
        $stream = new Stream($resource);

        $parser = new StreamingJsonParser();

        $xf = t\comp(
          t\map('Nab3aBundle\Process\mapTweet'),
          t\map('Nab3aBundle\Process\filterNulls')
        );

        while (!$stream->eof()) {
            $chunk = $stream->read(4096);
            $objects = $parser->push($chunk);
            $objects = t\xform($objects, $xf);

            foreach ($objects as $object) {
                switch ($object['id']) {
                    case '717696904906350600':
                        $this->assertEquals('2016-04-06 12:54:10', $object['created_at']);
                        $this->assertEquals('2007-05-23 06:01:13', $object['user']['created_at']);
                        $this->assertEquals('https://twittercommunity.com/t/coming-soon-improved-image-sizes-to-the-api/64601', $object['entities']['urls']);
                        $this->assertEquals('http://twitter.com/twitterapi/status/717696904906350592/photo/1', $object['entities']['media']);
                        break;
                    case '714866168243351600':
                        $this->assertEquals('https://twitter.com/TwitterDev/status/714845041370464256', $object['entities']['urls']);
                        $this->assertEquals('https://blog.twitter.com/2016/alt-text-support-for-twitter-cards-and-the-rest-api', $object['quoted_status']['entities']['urls']);
                        $this->assertEquals('https://dev.twitter.com/', $object['quoted_status']['user']['entities']['url']['urls']);
                        break;
                    case '705441724689223700':
                        $this->assertEquals('2016-03-03 16:56:48', $object['retweeted_status']['created_at']);
                        break;
                }
            }
        }
    }
}
