<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SampleCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('stream:sample')
          ->addArgument('destination', InputArgument::OPTIONAL, 'destination for stream');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = $this->container->get('event_loop');
        $params = [];
        $promise = $this->container->get('twitter_stream.request_factory')->sample($params);
        $promise = $this->container->get('twitter_stream.stream_factory')->stream($promise);
        $promise = $this->container->get('twitter_stream.message_emitter')->messages($promise);

        $loop->run();
    }
}
