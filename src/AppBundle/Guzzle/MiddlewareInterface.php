<?php

namespace AppBundle\Guzzle;

use GuzzleHttp\HandlerStack;

interface MiddlewareInterface
{
    public function push(HandlerStack $stack);
}
