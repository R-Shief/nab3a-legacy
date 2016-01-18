<?php

namespace AppBundle\Command;

use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StdioCommand extends Command
{
    protected function configure()
    {
        $this->setName('demo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Factory::create();

        $io = new Stdio($loop);
        $io->getReadline()->setPrompt('Input > ');

        $io->on('line', function ($line) use (&$stdio, $output) {
            switch ($line) {
                case 'v':
                    $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
                    break;
                case 'vv':
                    $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
                    break;
                case 'vvv':
                    $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
                    break;
            }
            if ($line === 'quit') {
                $stdio->end();
            }
        });

        $loop->run();
    }
}
