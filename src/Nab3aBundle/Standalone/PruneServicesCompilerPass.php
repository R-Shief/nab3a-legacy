<?php

namespace Nab3aBundle\Standalone;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class PruneServicesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Classes that implement these interfaces are numerous and have
        // to be brute force removed from the container.
        $removeClasses = [
          'Symfony\\Component\\HttpKernel\\Fragment\\FragmentRendererInterface',
          'Symfony\\Component\\Translation\\Loader\\LoaderInterface',
          'Symfony\\Component\\Translation\\Dumper\\DumperInterface',
          'Symfony\\Component\\Translation\\Extractor\\ExtractorInterface',
          'Monolog\\Formatter\\FormatterInterface',
        ];

        $callback = function (Definition $definition) use ($removeClasses) {
            return is_a($definition->getClass(), $removeClass, true);
        };
        $definitions = array_filter($container->getDefinitions(), $callback);

        $container->setDefinitions($definitions);

        $container->removeDefinition('cache_clearer');
        $container->removeDefinition('cache_warmer');
        $container->removeDefinition('fragment.handler');
        $container->removeDefinition('http_kernel');
        $container->removeDefinition('kernel.class_cache.cache_warmer');
        $container->removeDefinition('locale_listener');
        $container->removeDefinition('monolog.logger.translation');
        $container->removeDefinition('request_stack');
        $container->removeDefinition('response_listener');
        $container->removeDefinition('streamed_response_listener');
        $container->removeDefinition('translation.loader');
        $container->removeDefinition('translation.writer');
        $container->removeDefinition('translator_listener');
        $container->removeDefinition('translator.default');
        $container->removeDefinition('uri_signer');
    }
}
