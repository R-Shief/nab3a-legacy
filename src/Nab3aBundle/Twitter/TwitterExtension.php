<?php

namespace Nab3aBundle\Twitter;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class TwitterExtension extends ConfigurableExtension
{
    /**
   * Configures the passed container according to the merged configuration.
   *
   * @param array $mergedConfig
   * @param ContainerBuilder $container
   */
  protected function loadInternal(
    array $mergedConfig,
    ContainerBuilder $container
  ) {
      $container->setParameter('twitter_consumer_key', $mergedConfig['consumer_key']);
      $container->setParameter('twitter_consumer_secret', $mergedConfig['consumer_secret']);
      $container->setParameter('twitter_access_token', $mergedConfig['access_token']);
      $container->setParameter('twitter_access_token_secret', $mergedConfig['access_token_secret']);
  }
}
