<?php

namespace Nab3aBundle\Output;

use Evenement\EventEmitterInterface;
use League\Pipeline\Pipeline;
use Nab3aBundle\Evenement\PluginInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Process\ProcessUtils;
use transducers as t;

class BufferOutputPlugin implements PluginInterface
{
    use LoggerAwareTrait;
    use ContainerAwareTrait;

    /**
     * @var string
     */
    private $buffer;

    /**
     * @var array
     */
    private $map;

    /**
     * @var
     */
    private $documentId;

    /**
     * @var
     */
    private $sheetId;

    public function __construct($map, $documentId, $sheetId)
    {
        $this->buffer = [];
        $this->map = $map;
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
        $cmd = 'stream:write:google -vvv ';
        $cmd .= ProcessUtils::escapeArgument($this->documentId).' ';
        $cmd .= ProcessUtils::escapeArgument($this->sheetId);

        $processBuilder = $this->container->get('nab3a.process.child_process');
        $process = $processBuilder->makeChildProcess($cmd);

        $process->stderr->pipe($this->container->get('nab3a.console.logger_helper'));
        $process->stdout->pipe($this->container->get('nab3a.console.logger_helper'));

        $xf = t\comp(
          t\map('Nab3aBundle\Process\mapTweet'),
          t\mapcat(function ($v) {
              return array_map(function ($key, $path) use ($v) {
                  $value = \igorw\get_in($v, $path);

                  return [$key, $value ?: ''];
              }, array_keys($this->map), array_values($this->map));
          })
        );

        $pipeline = new Pipeline([
          function (array $data) use ($xf) { return t\xform([$data], $xf); },
          function (array $assoc) { return t\transduce('transducers\identity', t\assoc_reducer(), $assoc); },
          'GuzzleHttp\json_encode',
        ]);

        $emitter->on('tweet', function (array $data) use ($pipeline, $process) {
            $data = $pipeline->process($data);
            $process->stdin->write($data);
        });
    }
}
