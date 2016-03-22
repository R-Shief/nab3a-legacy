<?php

namespace Nab3aBundle\Standalone;

interface ParameterProviderInterface
{
    /**
     * Provide parameters for a RuntimeParameterBag as a key/value hash.
     *
     * @return array
     */
    public function getParametersAsKeyValueHash();
}
