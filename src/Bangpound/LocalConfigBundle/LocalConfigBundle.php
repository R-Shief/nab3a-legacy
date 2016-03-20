<?php

namespace Bangpound\LocalConfigBundle;

use Bangpound\LocalConfigBundle\DependencyInjection\LocalConfigExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LocalConfigBundle extends Bundle
{
    /**
   * @var array
   */
  private $extensions;

    public function __construct(array $extensions = array())
    {
        $this->extensions = $extensions;
    }

    protected function createContainerExtension()
    {
        return new LocalConfigExtension($this->extensions);
    }
}
