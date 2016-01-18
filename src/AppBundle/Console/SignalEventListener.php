<?php

namespace AppBundle\Console;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SignalEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
          ConsoleEvents::COMMAND => array('onCommand'),
          ConsoleEvents::TERMINATE => array('onTerminate'),
        );
    }

    public function onCommand(ConsoleCommandEvent $event)
    {
        pcntl_signal(SIGUSR1, function (int $signo) {
            echo $signo;
            exit;
        });
        pcntl_signal(SIGINT, function (int $signo) {
            echo $signo;
            exit;
        });
    }

    public function onTerminate(ConsoleTerminateEvent $event)
    {
    }
}
