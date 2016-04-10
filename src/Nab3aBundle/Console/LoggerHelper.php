<?php

namespace Nab3aBundle\Console;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoggerHelper
{
    use ContainerAwareTrait;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function onData($chunk)
    {
        try {
            $data = \GuzzleHttp\json_decode($chunk, true);
            $id = 'monolog.logger';
            if (isset($data['channel']) && $data['channel'] !== 'app') {
                $id .= '.'.$data['channel'];
            }
            /** @var LoggerInterface $logger */
            $logger = $this->container->get($id);
            $logger->log($data['level'], $data['message'], $data['context']);
        } catch (\InvalidArgumentException $e) {
            $this->output->write($chunk);
        }
    }
}
