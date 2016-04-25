<?php

namespace Nab3aBundle\Stream;

use Nab3aBundle\Console\AbstractCommand;
use React\ChildProcess\Process;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PipeCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();
        $this
          ->setName('pipe')
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

        $process = $this->container
          ->get('nab3a.process.child_process')
          ->makeChildProcess('stream:read:twitter '.$input->getArgument('name'));

        $process->stderr->pipe($this->container->get('nab3a.console.logger_helper'));
        $process->stdout->pipe($this->container->get('nab3a.twitter.message_emitter'));

        $this->attachListeners($process);

        $loop->run();
    }

    /**
     * @param Process $process
     */
    private function attachListeners(Process $process)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $listener = function (ConsoleEvent $event) use ($process) {
            $process->terminate();
            usleep(self::CHILD_PROC_TIMER * 1e6);
        };
        $dispatcher->addListener(ConsoleEvents::EXCEPTION, $listener);
        $dispatcher->addListener(ConsoleEvents::TERMINATE, $listener);
    }
}
