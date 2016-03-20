<?php

namespace Nab3aBundle\Command;

use Nab3aBundle\Model\StreamParameters;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ValidationCommand extends AbstractCommand
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this->setName('validate');
    }

    /**
     * This is all made up!
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return StreamParameters
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = new StreamParameters();
        $query->setFollow([12345, 67890]);
        $query->setTrack(['jade goody', 'charlie tuna']);
        $query->setLocations([
          [-122.75, 36.8, -121.75, 37.8],
          [-74, 40, -73, 41],
        ]);

        // Benjamin really wants to know this. It's not an idle question.
        $result = $this->container->get('validator')->validate($query);

        return $result;
    }
}