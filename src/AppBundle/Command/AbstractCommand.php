<?php

namespace AppBundle\Command;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractCommand extends Command
{
    use LoggerAwareTrait;
    use ContainerAwareTrait;

    public function getContainer()
    {
        return $this->container;
    }
}
