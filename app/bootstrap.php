<?php

$loader = require __DIR__.'/../app/autoload.php';
require __DIR__ .'/../app/ContainerKernel.php';

$kernel = new ContainerKernel('prod', false);
$kernel->boot();
$kernel->shutdown();
