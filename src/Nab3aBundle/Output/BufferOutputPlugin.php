<?php

namespace Nab3aBundle\Output;

use Evenement\EventEmitterInterface;
use Nab3aBundle\Evenement\PluginInterface;
use Psr\Log\LoggerAwareTrait;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;

class BufferOutputPlugin implements PluginInterface
{
    use LoggerAwareTrait;
    use ContainerAwareTrait;

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
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;
    /**
     * @var
     */
    private $documentId;
    /**
     * @var
     */
    private $sheetId;

    public function __construct($size, $map, LoopInterface $loop, $documentId, $sheetId)
    {
        $this->buffer = [];
        $this->size = $size;
        $this->map = $map;
        $this->loop = $loop;
        $this->documentId = $documentId;
        $this->sheetId = $sheetId;
    }

    /**
     * @param EventEmitterInterface $emitter
     *
     * @return mixed
     */
    public function attachEvents(EventEmitterInterface $emitter)
    {
        $filter = self::makeCallback($this->map);
        $emitter->on('tweet', function ($data) use ($filter) {
            $this->buffer[] = $filter($data);
            if (count($this->buffer) === $this->size) {
                $data = $this->buffer;
                array_unshift($data, array_keys($this->map));

                $exec = $_SERVER['argv'][0];

                $process = new Process('exec '.$exec.' output:google --child -vvv '.$this->documentId.' '.$this->sheetId);
                $process->on('exit', function ($code, $signal) {
                    $this->logger->debug('Exit code '.$code);
                });

                $process->start($this->loop);

                $process->stderr->on('data', json_stream_callback([$this->container->get('nab3a.console.logger_helper'), 'onData']));
                $process->stdout->on('data', json_stream_callback([$this->container->get('nab3a.console.logger_helper'), 'onData']));

                $process->stdin->end(json_encode($data));

                $this->buffer = [];
            }
        });
    }

    public static function makeCallback($map)
    {
        $propertyAccess = PropertyAccess::createPropertyAccessor();

        return function ($data) use ($propertyAccess, $map) {
        $tweet = \GuzzleHttp\json_decode($data, true);

        array_walk_recursive($tweet, function (&$value, $key) {
            if ($key === 'created_at') {
                $value = \DateTime::createFromFormat('D M j H:i:s P Y', $value)->format('Y-m-d H:i:s');
            }
        }, $tweet);

        $row = [];
        foreach ($map as $path) {
            $value = $propertyAccess->getValue($tweet, $path);

            if (is_array($value)) {

                // Flatten the entities.
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

                $value = implode(', ', $value);
            }

            if (is_null($value)) {
                $value = '';
            }

            $row[] = $value;
        }

        return $row;
        };
    }
}
