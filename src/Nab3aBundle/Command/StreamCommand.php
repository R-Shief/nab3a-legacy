<?php

namespace Nab3aBundle\Command;

use GuzzleHttp\Exception\RequestException;
use Nab3aBundle\Stream\TwitterStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
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

        $rf = $this->container->get('nab3a.twitter.request_factory');
        $me = $this->container->get('nab3a.twitter.message_emitter');

        $promise = $rf
          ->fromStreamConfig($this->params);

        $stream = $promise
          ->then(function (ResponseInterface $response) {
              return $response->getBody();
          }, function (RequestException $e) { throw $e; })
          ->then(function (StreamInterface $s) use ($me, $loop) {
              $stream = new TwitterStream($s, $loop);
              $me->attachEvents($stream);

              return $stream;
          });

        $stream->wait();

        $loop->run();
    }
}
