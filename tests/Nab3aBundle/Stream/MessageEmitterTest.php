<?php

namespace Nab3aBundle\Tests\Stream;

use Nab3aBundle\Twitter\MessageEmitter;
use Nab3aBundle\Twitter\TypeGuesser;

class MessageEmitterTest extends \PHPUnit_Framework_TestCase
{
    public function testOnTweet()
    {
        $guesser = new TypeGuesser();
        $emitter = new MessageEmitter($guesser);

        $emitter->once('tweet', function ($data) {
            $this->assertEquals([
              'created_at' => '',
              'text' => '',
            ], $data);
        });
        $emitter->write('{"created_at":"","text":""}');

        $emitter->once('event', function ($data) {
            $this->assertEquals([
                'event' => [],
            ], $data);
        });
        $emitter->write('{"event":[]}');

        $emitter->once('keep-alive', function () {
            $this->assertTrue(true);
        });
    }
}
