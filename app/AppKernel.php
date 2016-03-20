<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\Loader;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
          new Bangpound\LocalConfigBundle\LocalConfigBundle(
              [
                  new Nab3aBundle\Twitter\TwitterExtension(),
                  new Nab3aBundle\Nab3a\Nab3aExtension(),
              ]
          ),
          new Nab3aBundle\Nab3aBundle($this),
          new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
          new Symfony\Bundle\MonologBundle\MonologBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
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

        // Property access is used by both the Form and the Validator component
        $loader->load(function (ContainerBuilder $container) {
            $container->removeDefinition('uri_signer');
            $container->removeDefinition('translator');
        });
    }
}
