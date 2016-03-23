<?php

$loader = require __DIR__.'/../app/autoload.php';
require __DIR__.'/../app/ContainerBuilderKernel.php';

$kernel = new ContainerBuilderKernel('prod', false);
$kernel->boot();
$kernel->shutdown();
