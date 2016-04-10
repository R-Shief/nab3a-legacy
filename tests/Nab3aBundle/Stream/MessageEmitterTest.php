<?php

namespace Nab3aBundle\Tests\Stream;

use Evenement\EventEmitter;
use Nab3aBundle\Stream\MessageEmitter;
use Nab3aBundle\Stream\TypeGuesser;

class MessageEmitterTest extends \PHPUnit_Framework_TestCase
{
    public function testOnTweet()
    {
        $guesser = new TypeGuesser();
        $emitter = new MessageEmitter($guesser);
        $stream = new EventEmitter();
        $emitter->attachEvents($stream);
        $emitter->on('event', function ($data) {
            $this->assertEquals('{"event":[]}', $data);
        });
        $emitter->on('tweet', function ($data) {
            $this->assertEquals('{"created_at":"","text":""}', $data);
        });
        $stream->emit('data', [json_encode(['created_at' => '', 'text' => ''])]);
    }
}
