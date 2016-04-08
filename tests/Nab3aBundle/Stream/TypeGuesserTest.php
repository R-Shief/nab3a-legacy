<?php

namespace Nab3aBundle\Tests\Stream;

use Nab3aBundle\Stream\TypeGuesser;

class TypeGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEventName()
    {
        $guesser = new TypeGuesser();

        $event = $guesser->getEventName("\r\n");
        $this->assertEquals('keep-alive', $event);

        $event = $guesser->getEventName('{"event":"test"}');
        $this->assertEquals('event', $event);

        $event = $guesser->getEventName(
          '{"event2":["Tue Apr 05 01:58:28 +0000 2016","test"]}'
        );
        $this->assertEquals('event2', $event);

        $event = $guesser->getEventName(
          '{"created_at":"Tue Apr 05 01:58:28 +0000 2016","id":717169503529013248,"id_str":"717169503529013248"}'
        );
        $this->assertEquals('tweet', $event);

        $event = $guesser->getEventName(
          '{"delete":{"status":{"id":1234,"id_str":"1234","user_id":3,"user_id_str":"3"}}}'
        );
        $this->assertEquals('delete', $event);

        $event = $guesser->getEventName('{"limit":{"track":1234}}');
        $this->assertEquals('limit', $event);

        $event = $guesser->getEventName(
          '{"status_withheld":{"id":1234567890,"user_id":123456,"withheld_in_countries":["DE","AR"]}}'
        );
        $this->assertEquals('status_withheld', $event);

        $event = $guesser->getEventName(
          '{"user_withheld":{"id":123456,"withheld_in_countries":["DE","AR"]}}'
        );
        $this->assertEquals('user_withheld', $event);

        $event = $guesser->getEventName(
          '{"disconnect":{"code":4,"stream_name":"","reason":""}}'
        );
        $this->assertEquals('disconnect', $event);

        $event = $guesser->getEventName(
          '{"warning":{"code":"FALLING_BEHIND","message":"Your connection is falling behind and messages are being queued for delivery to you. Your queue is now over 60% full. You will be disconnected when the queue is full.","percent_full":60}}'
        );
        $this->assertEquals('warning', $event);

        $event = $guesser->getEventName(
          '{"created_at":"Tue Aug 06 02:23:21 +0000 2013","source":{},"target":{},"event":"user_update"}'
        );
        $this->assertEquals('user_update', $event);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGoodJsonInput()
    {
        $guesser = new TypeGuesser();

        $event = $guesser->getEventName(
          '{"created_at":"Tue Apr 05 01:58:28 +0000 2016","id":717169503529013248,"id_str":"717169503529013248"}\\r\\n'
        );
        $this->assertEquals('tweet', $event);
    }
}
