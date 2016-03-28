<?php

namespace Nab3aBundle\Output;

use Evenement\EventEmitterInterface;
use Nab3aBundle\Evenement\PluginInterface;
use Nab3aBundle\Google\SheetStream;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class BufferOutputPlugin implements PluginInterface
{
    /**
     * @var string
     */
    private $buffer;
    /**
     * @var int
     */
    private $size;
    /**
     * @var array
     */
    private $map;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccess;

    /**
     * @var SheetStream
     */
    private $stream;

    public function __construct($size, $map, SheetStream $stream)
    {
        $this->buffer = [];
        $this->size = $size;
        $this->map = $map;
        $this->propertyAccess = PropertyAccess::createPropertyAccessor();
        $this->stream = $stream;
    }

    /**
     * @param EventEmitterInterface $emitter
     *
     * @return mixed
     */
    public function attachEvents(EventEmitterInterface $emitter)
    {
        $emitter->on('tweet', function ($data) {
            $this->buffer[] = $this->filterData($data);
            if (count($this->buffer) === $this->size) {
                $data = $this->buffer;
                $this->stream->write($data);
                $this->buffer = [];
            }
        });
    }

    private function filterData($data)
    {
        $tweet = json_decode($data, true);

        array_walk_recursive($tweet, function (&$value, $key) {
            if ($key === 'created_at') {
                $value = \DateTime::createFromFormat('D M j H:i:s P Y', $value)->format('Y-m-d H:i:s');
            }
        }, $tweet);

        $row = [];
        foreach ($this->map as $path) {
            $value = $this->propertyAccess->getValue($tweet, $path);

            if (is_array($value)) {
                $value = array_map(function ($value) {
                    if (isset($value['expanded_url'])) {
                        return $value['expanded_url'];
                    }
                    if (isset($value['text'])) {
                        return $value['text'];
                    }
                    if (isset($value['screen_name'])) {
                        return $value['screen_name'];
                    }
                }, $value);

                $value = implode(',', $value);
            }

            if (is_null($value)) {
                $value = '';
            }

            $row[] = $value;
        }

        return json_encode($row);
    }
}
