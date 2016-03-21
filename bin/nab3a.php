<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

set_time_limit(0);

$loader = require __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);
require_once __DIR__ .'/../app/cache/prod/appProdProjectContainer.php';

$container = new appProdProjectContainer();
$app = $container->get('console.application');
$app->run();
