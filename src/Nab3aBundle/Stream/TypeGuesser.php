<?php

namespace Nab3aBundle\Stream;

class TypeGuesser
{
    /**
     * @param $message
     *
     * @return string
     */
    public function getEventName($message)
    {
        if ($message === "\r\n") {
            $event = 'keep-alive';
        } else {
            $data = json_decode($message, true);
            $event = count($data) > 1 ? 'tweet' : key($data);
        }

        return $event;
    }
}
