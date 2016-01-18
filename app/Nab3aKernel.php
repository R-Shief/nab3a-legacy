<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\Loader;

class Nab3aKernel extends Kernel
{
    use Bangpound\Kernel\ClassBasedNameTrait;
    use Bangpound\Kernel\YamlEnvironmentTrait;


    public function registerBundles()
    {
        $bundles = [
          new AppBundle\AppBundle($this),
          new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
          new Symfony\Bundle\MonologBundle\MonologBundle(),
          new Matthias\SymfonyConsoleForm\Bundle\SymfonyConsoleFormBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
        };

        return $bundles;
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config.yml');
        $loader->load(function (ContainerBuilder $container) {
            $container->addObjectResource($this);
        });

        $loader->load('nab3a.yml');
        $loader->load(function (ContainerBuilder $container) {
            $container->addObjectResource($this);
        });

        // Property access is used by both the Form and the Validator component
        $loader->load(function (ContainerBuilder $container) {
            $container->removeDefinition('uri_signer');
            $container->removeDefinition('translator');
        });
    }

    protected function getContainerLoader(ContainerInterface $container)
    {
        /* @var ContainerBuilder $container */
        $locator = new FileLocator([getcwd(), $this->getRootDir(), getenv('HOME').'/.rshief']);

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
