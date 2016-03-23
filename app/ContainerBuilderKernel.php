<?php

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Config\EnvParametersResource;
use Symfony\Component\HttpKernel\DependencyInjection\AddClassesToCachePass;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;

class ContainerBuilderKernel extends Kernel
{
    protected $loadClassCache = false;

    public function registerBundles()
    {
        $bundles = [
          new Nab3aBundle\Nab3aBundle(),
          new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
          new Symfony\Bundle\MonologBundle\MonologBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config.yml');
    }

    protected function getContainerClass()
    {
        return 'ProjectServiceContainer';
    }

    final public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        throw new RuntimeException();
    }

    final public function terminate(Request $request, Response $response)
    {
        return;
    }

    protected function initializeContainer()
    {
        $class = $this->getContainerClass();
        $file = $this->rootDir.'/build/container.php';
        $container = $this->buildContainer();
        $container->compile();
        $this->dumpContainerToFile($file, $container, $class, $this->getContainerBaseClass());

        require_once $file;
        $this->container = new $class();
    }

    protected function dumpContainerToFile($file, ContainerBuilder $container, $class, $baseClass) {
        $dumper = new PhpDumper($container);
        $content = $dumper->dump(array('class' => $class, 'base_class' => $this->getContainerBaseClass(), 'file' => $file));
        if (!$this->debug) {
            $content = static::stripComments($content);
        }
        file_put_contents($file, $content);
    }

    protected function buildContainer()
    {
        $container = $this->getContainerBuilder();
        $container->addObjectResource($this);
        $this->prepareContainer($container);

        if (null !== $cont = $this->registerContainerConfiguration($this->getContainerLoader($container))) {
            $container->merge($cont);
        }

        $container->addCompilerPass(new AddClassesToCachePass($this));
        $container->addResource(new EnvParametersResource('SYMFONY__'));

        return $container;
    }

}
