<?php

namespace Nab3aBundle;

use Matthias\BundlePlugins\BundleWithPlugins;
use Nab3aBundle\Console\ConsolePlugin;
use Nab3aBundle\Evenement\EvenementPlugin;
use Nab3aBundle\EventLoop\EventLoopPlugin;
use Nab3aBundle\Google\GooglePlugin;
use Nab3aBundle\Guzzle\GuzzlePlugin;
use Nab3aBundle\Logger\LoggerPlugin;
use Nab3aBundle\Standalone\StandalonePlugin;
use Nab3aBundle\Stream\StreamPlugin;
use Nab3aBundle\Twitter\TwitterPlugin;
use Nab3aBundle\Watch\WatchPlugin;

class Nab3aBundle extends BundleWithPlugins
{
    protected function getAlias()
    {
        return 'nab3a';
    }

    protected function alwaysRegisteredPlugins()
    {
        return [
          new ConsolePlugin(),
          new EvenementPlugin(),
          new EventLoopPlugin(),
          new GooglePlugin(),
          new GuzzlePlugin(),
          new LoggerPlugin(),
          new StandalonePlugin(),
          new StreamPlugin(),
          new TwitterPlugin(),
          new WatchPlugin(),
        ];
    }
}
