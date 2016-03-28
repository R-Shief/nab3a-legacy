<?php

namespace Nab3aBundle\Standalone;

use Matthias\BundlePlugins\ConfigurationWithPlugins;
use Matthias\BundlePlugins\ExtensionWithPlugins;
use Nab3aBundle\Google\GooglePlugin;
use Nab3aBundle\Loader\YamlFileLoader;
use Nab3aBundle\Stream\StreamPlugin;
use Nab3aBundle\Twitter\TwitterPlugin;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ParameterProvider implements ParameterProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    private static function getConfiguration($name)
    {
        return new ConfigurationWithPlugins($name, [
          new GooglePlugin(),
          new StreamPlugin(),
          new TwitterPlugin(),
        ]);
    }

    public function getParametersAsKeyValueHash()
    {
        $paths = [$_SERVER['HOME'].'/.rshief', getcwd()];
        $locator = new FileLocator($paths);
        $loader = new YamlFileLoader($locator);

        $resources = $locator->locate($this->name.'.yml', null, false);

        $configs = array_map(function ($resource) use ($loader) {
            $configs = $loader->load($resource);

            return $configs[$this->name];
        }, $resources);

        /* @var ExtensionWithPlugins $extension */
        $configuration = self::getConfiguration($this->name);
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);
        $params = [];

        foreach (array_keys($config) as $l2) {
            foreach (array_keys($config[$l2]) as $l3) {
                $key = implode('.', [$this->name, $l2, $l3]);
                $params[$key] = $config[$l2][$l3];
            }
        }

        return $params;
    }
}
