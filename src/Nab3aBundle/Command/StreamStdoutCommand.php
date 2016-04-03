<?php

namespace Nab3aBundle\Command;

use Nab3aBundle\Stream\TwitterStream;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StreamStdoutCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();
        $this
          ->setName('stream:stdout')
          ->setDescription('Connect to a streaming API endpoint and collect data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rf = $this->container->get('nab3a.twitter.request_factory');

        /** @var ResponseInterface $response */
        $response = $rf->fromStreamConfig($this->params)->wait();
        $stream = $response->getBody();

        while (!$stream->eof() && $stream->isReadable()) {
            $data = TwitterStream::handleData($stream);
            $output->write($data, false, OutputInterface::OUTPUT_RAW);
        }
    }
}
