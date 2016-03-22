<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

set_time_limit(0);

$loader = require __DIR__.'/../app/autoload.php';

require_once __DIR__ .'/../app/cache/prod/appProdProjectContainer.php';

$container = new appProdProjectContainer();
$application = $container->get('nab3a.console.application');
$application->run();
