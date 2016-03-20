<?php

namespace AppBundle\Nab3a;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class Nab3aExtension extends ConfigurableExtension
{
    /**
     * Configures the passed container according to the merged configuration.
     *
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setParameter('nab3a', $mergedConfig);
        $container->register('twitter_stream.watcher.streaming_parameters', 'AppBundle\StreamParameter\Watcher');
    }
}
