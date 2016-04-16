<?php

namespace Nab3aBundle\DependencyInjection;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class BundlePlugin extends SimpleBundlePlugin
{
    protected $name;

    public function name()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $className = get_class($this);
        if (substr($className, -6) != 'Plugin') {
            throw new BadMethodCallException('This extension does not follow the naming convention; you must overwrite the getAlias() method.');
        }
        $classBaseName = substr(strrchr($className, '\\'), 1, -6);

        $this->name = Container::underscore($classBaseName);

        return $this->name;
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $r = new \ReflectionClass($this);
        $loader = new YamlFileLoader($container, new FileLocator([dirname($r->getFileName())]));
        $loader->load('config.yml');
    }
}
