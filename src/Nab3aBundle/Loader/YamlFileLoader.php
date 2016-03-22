<?php

namespace Nab3aBundle\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader extends FileLoader
{
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        $content = Yaml::parse(file_get_contents($path));

        return $content;
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && in_array(pathinfo($resource, PATHINFO_EXTENSION), array('yml', 'yaml'), true);
    }
}
