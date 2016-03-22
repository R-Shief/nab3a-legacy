<?php

namespace Nab3aBundle\Standalone;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

class RuntimeParameterBag extends FrozenParameterBag implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use LoggerAwareTrait;

    private $initialized = false;

    /**
     * @var \Nab3aBundle\Standalone\ParameterProviderInterface
     */
    private $parameterProvider;

    public function __construct(ParameterProviderInterface $parameterProvider)
    {
        parent::__construct();
        $this->parameterProvider = $parameterProvider;
    }

    public function all()
    {
        $this->initialize();

        return parent::all();
    }

    public function get($name)
    {
        $this->initialize();

        return parent::get($name);
    }

    public function has($name)
    {
        $this->initialize();

        return parent::has($name);
    }

    public function deinitialize()
    {
        $this->parameters = array();
        $this->initialized = false;
    }

    private function initialize()
    {
        if ($this->initialized) {
            return;
        }
        $this->parameters = $this->parameterProvider->getParametersAsKeyValueHash();
        $this->initialized = true;
    }
}
