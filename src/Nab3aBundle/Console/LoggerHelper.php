<?php

namespace Nab3aBundle\Console;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoggerHelper
{
    use ContainerAwareTrait;
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function onData($chunk)
    {
        $data = \GuzzleHttp\json_decode($chunk, true);
        if ($data) {
            $id = $data['channel'] === 'app' ? 'logger' : 'monolog.logger.'.$data['channel'];
            /** @var LoggerInterface $logger */
            $logger = $this->container->get($id);
            $logger->log(
              $data['level'],
              $data['message'],
              $data['context']
            );
        } else {
            $this->output->write($chunk);
        }
    }
}
