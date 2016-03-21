<?php

namespace Nab3aBundle\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Application extends BaseApplication
{
    use ContainerAwareTrait;

    const VERSION = '0';
    const VERSION_ID = 0;
    const MAJOR_VERSION = 0;
    const MINOR_VERSION = 0;
    const RELEASE_VERSION = 0;
    const EXTRA_VERSION = '';
}
