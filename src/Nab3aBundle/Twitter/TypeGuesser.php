<?php

namespace Nab3aBundle\Twitter;

class TypeGuesser
{
    /**
     * @param $message
     *
     * @return string
     */
    public function getEventName($message)
    {
        // Blank lines are a keep-alive signal.
        if ($message === "\r\n") {
            $event = 'keep-alive';
        } else {
            $data = \GuzzleHttp\json_decode($message, true);
            // Twitter public stream messages that decode as objects with only
            // one property are events of that type. Events may also be objects
            // with multiple properties including an `event` property. Other
            // message objects many properties are a standard Tweet payload.
            // @see <https://dev.twitter.com/streaming/overview/messages-types>
            $event = count($data) > 1 ? (isset($data['event']) ? $data['event'] : 'tweet') : key($data);
        }

        return $event;
    }
}
