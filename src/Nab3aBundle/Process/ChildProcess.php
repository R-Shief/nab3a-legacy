<?php

namespace Nab3aBundle\Process;

use Nab3aBundle\Console\LoggerHelper;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use Symfony\Component\Process\ProcessUtils;

class ChildProcess
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var LoggerHelper
     */
    private $loggerHelper;

    /**
     * ChildProcess constructor.
     *
     * @param LoopInterface $loop
     * @param LoggerHelper  $loggerHelper
     */
    public function __construct(LoopInterface $loop, LoggerHelper $loggerHelper)
    {
        $this->loop = $loop;
        $this->loggerHelper = $loggerHelper;
    }

    /**
     * @param $cmd
     * @param null  $cwd
     * @param array $env
     * @param array $options
     *
     * @return Process
     */
    public function createChildProcess($cmd, $cwd = null, array $env = null, array $options = array())
    {
        $cmd = 'exec php '.ProcessUtils::escapeArgument($_SERVER['argv'][0]).' --child '.$cmd;

        $process = new Process($cmd, $cwd, $env, $options);
        $process->start($this->loop);
        $process->stderr->on('data', line_delimited_stream([$this->loggerHelper, 'onData']));
        $process->stdout->on('data', line_delimited_stream([$this->loggerHelper, 'onData']));

        return $process;
    }
}
