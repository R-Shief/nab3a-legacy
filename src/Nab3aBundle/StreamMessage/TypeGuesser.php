<?php

namespace Nab3aBundle\StreamMessage;

use Symfony\Component\Serializer\Serializer;

class TypeGuesser
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * MessageClassifier constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

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
            $data = $this->serializer->decode($message, 'json');
            $event = count($data) > 1 ? 'tweet' : key($data);
        }

        return $event;
    }
}
