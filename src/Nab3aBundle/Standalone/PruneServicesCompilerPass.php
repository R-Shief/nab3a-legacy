<?php

namespace Nab3aBundle\Standalone;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class PruneServicesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $removeClasses = [
          'Symfony\\Component\\HttpKernel\\Fragment\\FragmentRendererInterface',
          'Symfony\\Component\\Translation\\Loader\\LoaderInterface',
          'Symfony\\Component\\Translation\\Dumper\\DumperInterface',
          'Symfony\\Component\\Translation\\Extractor\\ExtractorInterface',
          'Monolog\\Formatter\\FormatterInterface',
        ];

        $definitions = array_filter($container->getDefinitions(), function (Definition $definition) use ($removeClasses) {
            return empty(array_filter(array_map(function ($removeClass) use ($definition) {
                return is_a($definition->getClass(), $removeClass, true);
            }, $removeClasses)));
        });

        unset($definitions['cache_warmer']);
        unset($definitions['fragment.handler']);
        unset($definitions['http_kernel']);
        unset($definitions['uri_signer']);

        unset($definitions['translation.loader']);
        unset($definitions['translation.writer']);
        unset($definitions['translator_listener']);
        unset($definitions['translator.default']);
        unset($definitions['monolog.logger.translation']);
        unset($definitions['request_stack']);
        unset($definitions['streamed_response_listener']);

        unset($definitions['locale_listener']);
        unset($definitions['response_listener']);

        $container->setDefinitions($definitions);
    }
}
