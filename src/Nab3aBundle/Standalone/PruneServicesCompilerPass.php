<?php

namespace Nab3aBundle\Standalone;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;

class PruneServicesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definitions = array_filter($container->getDefinitions(), function (Definition $definition) {
            if (is_a($definition->getClass(), FragmentRendererInterface::class, true)) {
                return false;
            }

            if (is_a($definition->getClass(), LoaderInterface::class, true)) {
                return false;
            }
            if (is_a($definition->getClass(), DumperInterface::class, true)) {
                return false;
            }
            if (is_a($definition->getClass(), ExtractorInterface::class, true)) {
                return false;
            }

            return true;

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
