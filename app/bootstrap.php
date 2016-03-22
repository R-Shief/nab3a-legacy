<?php

$loader = require __DIR__.'/../app/autoload.php';

$proc = new \Symfony\Component\Process\Process('composer install --no-dev');
$proc->run();

$proc = new \Symfony\Component\Process\Process('bin/console cache:clear --env=prod --no-debug --no-warmup');
$proc->run();

$kernel = new AppKernel('prod', false);
$kernel->boot();
