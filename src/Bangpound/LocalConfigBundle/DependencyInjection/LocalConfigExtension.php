<?php

namespace Bangpound\LocalConfigBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LocalConfigExtension extends Extension
{
    /**
     * @var
     */
    private $extensions;

    public function __construct($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        $cont = $this->localContainer($config['filename']);
        $container->merge($cont);
    }

    protected function localContainer($filename)
    {
        $container = new ContainerBuilder();
        foreach ($this->extensions as $extension) {
            $container->registerExtension($extension);
        }
        $loader = $this->getLocalConfigLoader($container);
        $loader->load($filename);
        $container->compile();

        return $container;
    }

    protected function getLocalConfigLoader(ContainerBuilder $container, $cwd = null)
    {
        if (!$cwd) {
            $cwd = getcwd();
        }
        $locator = new FileLocator([$cwd]);

        $resolver = new LoaderResolver(array(
          new Loader\XmlFileLoader($container, $locator),
          new Loader\YamlFileLoader($container, $locator),
          new Loader\IniFileLoader($container, $locator),
          new Loader\PhpFileLoader($container, $locator),
          new Loader\DirectoryLoader($container, $locator),
          new Loader\ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }
}
