<?php

namespace Nab3aBundle\Watch;

use Nab3aBundle\Loader\LoaderHelper;
use Nab3aBundle\Twitter\TwitterPlugin;
use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StreamParameterWatch implements EventEmitterInterface
{
    use EventEmitterTrait;

    /**
     * @param LoopInterface $loop
     * @param $resource
     * @param int $interval
     *
     * @return TimerInterface
     */
    public function watch(LoopInterface $loop, $resource, $interval = 15)
    {
        // The listener notices changes in the streaming filter parameters.
        $function = $this->listenerFactory($resource);

        // Load the parameters at startup.
        $loop->nextTick($function);

        // Also schedule them to be watched.
        return $loop->addPeriodicTimer($interval, $function);
    }

    /**
     * Returns a listener that notices when a resource changes.
     *
     * @param $resource mixed anything that can be loaded
     *
     * @return \Closure
     */
    private function listenerFactory($resource)
    {
        return function () use ($resource, &$current) {
            $cont = new ContainerBuilder();
            $cont->registerExtension(new TwitterPlugin());
            $cont->registerExtension(new Nab3aExtension());
            $loader = $this->getLocalConfigLoader($cont);
            $loader->load($resource);
            $cont->compile();
            $params = $cont->getParameterBag()->get('nab3a');
            if (!$current || $current !== $params) {
                $current = $params;
                $this->emit('filter_change', [LoaderHelper::makeQueryParams($params['track'], $params['follow'], $params['locations'])]);
            }
        };
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
