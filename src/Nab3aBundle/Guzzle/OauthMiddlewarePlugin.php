<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class OauthMiddlewarePlugin implements MiddlewareInterface
{
    /**
     * @var
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function push(HandlerStack $stack)
    {
        $stack->push(new Oauth1($this->config), 'oauth');
    }
}
