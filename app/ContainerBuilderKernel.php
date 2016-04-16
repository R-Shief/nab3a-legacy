<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Kernel;

class ContainerBuilderKernel extends Kernel
{
    protected $loadClassCache = false;

    /**
     * @var array
     */
    private $compilerLog;

    public function registerBundles()
    {
        $plugins = [];

        if ($this->isDebug()) {
            $plugins[] = new Nab3aBundle\Debug\DebugPlugin();
        }

        $bundles = [
          new Nab3aBundle\Nab3aBundle($plugins),
          new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
          new Symfony\Bundle\MonologBundle\MonologBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config.yml');
    }

    protected function initializeContainer()
    {
        $path = $this->rootDir.'/build/container.php';
        $container = new ContainerBuilder(new ParameterBag($this->getKernelParameters()));

        $extensions = array();
        foreach ($this->bundles as $bundle) {
            if ($extension = $bundle->getContainerExtension()) {
                $container->registerExtension($extension);
                $extensions[] = $extension->getAlias();
            }
        }
        foreach ($this->bundles as $bundle) {
            $bundle->build($container);
        }

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));

        if (null !== $cont = $this->registerContainerConfiguration($this->getContainerLoader($container))) {
            $container->merge($cont);
        }

        $container->compile();
        if (!$this->isDebug()) {
            $this->compilerLog = $container->getCompiler()->getLog();
        }

        // cache the container
        $dumper = new PhpDumper($container);
        $content = $dumper->dump(['file' => $path, 'debug' => $this->debug]);

        $mode = 0666;
        $umask = umask();
        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $content);
        try {
            $filesystem->chmod($path, $mode, $umask);
        } catch (IOException $e) {
            // discard chmod failure (some filesystem may not support it)
        }

        require_once $path;

        $this->container = new ProjectServiceContainer();
    }
}
