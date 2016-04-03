<?php

set_time_limit(0);

$loader = require __DIR__.'/../app/autoload.php';

require_once __DIR__ .'/../app/build/container.php';

$container = new ProjectServiceContainer();
$container->set('nab3a.console.input', new Symfony\Component\Console\Input\ArgvInput());
$container->set('nab3a.console.output', new Symfony\Component\Console\Output\ConsoleOutput());
$application = $container->get('nab3a.console.application');
$application->run($container->get('nab3a.console.input'), $container->get('nab3a.console.output'));
