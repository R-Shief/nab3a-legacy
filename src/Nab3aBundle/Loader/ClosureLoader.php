<?php

namespace Nab3aBundle\Loader;

use Symfony\Component\Config\Loader\Loader;

class ClosureLoader extends Loader
{
    /**
     * Loads a resource.
     *
     * @param \Closure    $resource The resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return array
     *
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $array = $resource();

        return LoaderHelper::makeQueryParams($array['track'], $array['follow'], $array['location']);
    }

    public function supports($resource, $type = null)
    {
        return $resource instanceof \Closure;
    }
}
