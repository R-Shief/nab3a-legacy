<?php

namespace Nab3aBundle\Command;

use React\ChildProcess\Process;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StreamCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();
        $this
          ->setName('stream')
          ->setDescription('Connect to a streaming API endpoint and collect data')
          ->addOption('watch', null, InputOption::VALUE_NONE, 'watch for stream configuration changes and reconnect according to API rules')
          ->addOption('out', null, InputOption::VALUE_OPTIONAL, 'output', STDOUT)
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = $this->container->get('nab3a.event_loop');

        // @todo
        //
        // we need a timer that keeps track of the time the current connection
        // was started, because we must avoid connection churning.
        //
        // filter parameters will change, we want to signal to the streaming
        // client that there it should reconnect, but if we don't accommodate
        // the fact that multiple changes could happen in a quick sequence, we'd
        // probably get blocked from the streaming API endpoints for too many
        // connection attempts.
        //
        // When this app receives those errors, it manages them correctly,
        // but it still stupidly allows these situations to arise.
        // $timer = $watcher->watch($resource);

        $exec = $_SERVER['argv'][0];

        $process = new Process('exec '.$exec.' stream:stdout --child -vvv '.$input->getArgument('name'));
        $process->on('exit', function ($code, $signal) {
            $this->logger->debug('Exit code '.$code);
        });

        $loop->addTimer(1e-3, function (TimerInterface $timer) use ($process) {
            $process->start($timer->getLoop());

            $process->stderr->on('data', json_stream_callback([$this->container->get('nab3a.console.logger_helper'), 'onData']));
            $process->stdout->on('data', json_stream_callback([$this->container->get('nab3a.twitter.message_emitter'), 'onData']));
        });

        $loop->run();
    }
}
