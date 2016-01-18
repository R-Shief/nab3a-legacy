<?php

namespace AppBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

class PidFileEventListener
{
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $inputDefinition = $event->getCommand()->getApplication()->getDefinition();

        $inputDefinition->addOption(
          new InputOption('pidfile', null, InputOption::VALUE_OPTIONAL, 'The location of the PID file that should be created for this process', null)
        );

        // merge the application's input definition
        $args = $event->getCommand()->mergeApplicationDefinition();
        // dump($event->getCommand()->getDefinition());

        $input = new ArgvInput();

        // we use the input definition of the command
        $input->bind($event->getCommand()->getDefinition());
        // dump($input);

        // dump($event->getCommand()->getDefinition());
        $pidFile = $input->getOption('pidfile');
    }

    public function onConsoleTerminate(ConsoleTerminateEvenr $event)
    {
    }
}
