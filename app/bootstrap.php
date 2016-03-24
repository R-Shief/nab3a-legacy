<?php

$loader = require __DIR__.'/../app/autoload.php';

$kernel = new ContainerBuilderKernel('prod', false);
$kernel->boot();
$kernel->shutdown();
