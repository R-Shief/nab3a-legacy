<?php

namespace Nab3aBundle\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerDebugCommand;
use Symfony\Bundle\FrameworkBundle\Command\TranslationUpdateCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class NoKernelCommandListener
{
    public function onCommand(ConsoleCommandEvent $event)
    {
        // get the command to be executed
        $command = $event->getCommand();

        if ($command instanceof ContainerDebugCommand || $command instanceof TranslationUpdateCommand) {
            //            $event->disableCommand();
        }
    }
}
