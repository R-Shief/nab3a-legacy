<?php

namespace Nab3aBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StreamCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
          ->setName('stream')
          ->addArgument('name', InputArgument::OPTIONAL, 'container parameter with filter parameters', 'default')
          ->addOption('watch', null, InputOption::VALUE_NONE, 'watch for stream configuration changes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = $this->container->get('event_loop');

        $name = 'nab3a.stream.'.$input->getArgument('name');
        $params = $this->container->get('nab3a.standalone.parameters')->get($name);

        $callback = function ($params) {
            $promise = $this->container->get('nab3a.twitter.request_factory')->fromStream($params);
            $promise = $this->container->get('nab3a.twitter.stream_factory')->stream($promise);
            $promise = $this->container->get('nab3a.twitter.message_emitter')->messages($promise);
        };

        if ($input->getOption('watch')) {
            $watcher = $this->container->get('watcher');
            $watcher->on('filter_change', $callback($params));
        } else {
            $callback($params);
        }

        // @todo
        //
        // we need a timer that keeps track of the time the current confection
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

        $loop->run();
    }
}
