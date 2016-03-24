<?php

namespace Nab3aBundle\Command;

use Psr\Log\LoggerAwareTrait;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractCommand extends Command
{
    use LoggerAwareTrait;
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $params;

    protected function configure()
    {
        parent::configure();
        $this->addArgument('name', InputArgument::OPTIONAL, 'container parameter with filter parameters', 'default');
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        $name = 'nab3a.stream.'.$input->getArgument('name');
        $this->params = $this->container->get('nab3a.standalone.parameters')->get($name);
    }
}
