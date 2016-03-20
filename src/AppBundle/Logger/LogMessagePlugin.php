<?php

namespace AppBundle\Logger;

use AppBundle\Evenement\PluginInterface;
use Evenement\EventEmitterInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class LogMessagePlugin implements PluginInterface
{
    use LoggerAwareTrait;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $events = [
      'delete' => LogLevel::NOTICE,
      'scrub_geo' => LogLevel::NOTICE,
      'limit' => LogLevel::NOTICE,
      'status_withheld' => LogLevel::NOTICE,
      'user_withheld' => LogLevel::NOTICE,
      'disconnect' => LogLevel::ERROR,
      'warning' => LogLevel::ERROR,
      'keep-alive' => LogLevel::DEBUG,
      'tweet' => LogLevel::DEBUG,
    ];

    /**
     * LogMessagePlugin constructor.
     *
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param array                                             $events
     */
    public function __construct(Serializer $serializer, array $events = [])
    {
        $this->serializer = $serializer;
    }

    public function attachEvents(EventEmitterInterface $emitter)
    {
        foreach ($this->events as $event => $level) {
            $emitter->on($event, $this->log($event, $level));
        }
    }

    private function log($event, $level = LogLevel::NOTICE)
    {
        return function ($data) use ($event, $level) {
            switch ($event) {
                case 'keep-alive':
                    $message = 'keep alive';
                    $context = [];
                    break;
                case 'tweet':
                    $tweet = json_decode($data, true);
                    $message = $tweet['text'];
                    $context = [
                        'user' => $tweet['user']['screen_name'],
                        'geo' => isset($tweet['geo']) || isset($tweet['coordinates']) || isset($tweet['place']),
                        'rt' => isset($tweet['retweeted_status']),
                        'reply' => isset($tweet['in_reply_to_status_id']),
                        'entities' => array_keys(array_filter($tweet['entities'])),
                        'lang' => $tweet['lang'],
                      ];
                    break;
                default:
                    $data = json_decode($data, true);
                    $message = $event;
                    $context = $data[$event];
                    break;
            }

            $this->logger->log($level, $message, $context);
        };
    }
}
