<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FilterCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('stream:filter')
          ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'container parameter with filter parameters', 'filter_parameters')
          ->addArgument('source', InputArgument::OPTIONAL, 'source for filter parameters [url or file]')
          ->addArgument('destination', InputArgument::OPTIONAL, 'destination for stream')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = $this->container->get('event_loop');
        $watcher = $this->container->get('twitter_stream.watcher.streaming_parameters');

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

        $watcher->on('filter_change', function ($params) {
            $promise = $this->container->get('twitter_stream.request_factory')->filter($params);
            $promise = $this->container->get('twitter_stream.stream_factory')->stream($promise);
            $promise = $this->container->get('twitter_stream.message_factory')->messages($promise);
        });

        $loop->run();
    }
}
