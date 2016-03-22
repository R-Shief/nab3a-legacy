<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

set_time_limit(0);

$loader = require __DIR__.'/../app/autoload.php';

require_once __DIR__ .'/../app/cache/prod/appProdProjectContainer.php';

$container = new appProdProjectContainer();
$app = $container->get('console.application');
$app->run();
