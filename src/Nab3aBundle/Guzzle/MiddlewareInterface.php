<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\HandlerStack;

interface MiddlewareInterface
{
    public function push(HandlerStack $stack);
}
