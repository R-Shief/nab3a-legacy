<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\Handler\Proxy;
use GuzzleHttp\Handler\StreamHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

class StackMiddlewareCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('guzzle.middleware');
        $stacks = [];
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $clientId = $tag['id'];
                $stacks[$clientId][] = $serviceId;
            }
        }

        foreach ($stacks as $clientId => $middlewareIds) {
            $clientDefinition = $container->getDefinition($clientId);
            $arguments = $clientDefinition->getArguments();

            $handlerDefinition = self::hasHandlerDefinition($arguments) ? self::getHandlerDefinition($arguments) : self::newHandlerDefinition($container);

            $stackDefinition = new DefinitionDecorator('nab3a.guzzle.handler_stack');
            $stackDefinition->setArguments([$handlerDefinition]);
            foreach ($middlewareIds as $middlewareId) {
                $stackDefinition->addMethodCall('push', [new Reference($middlewareId)]);
            }

            $arguments[0]['handler'] = $stackDefinition;

            $clientDefinition->setArguments($arguments);
            $container->setDefinition($clientId, $clientDefinition);
        }
    }

    private static function hasHandlerDefinition(array $arguments = [[]])
    {
        return isset($arguments[0]['handler']);
    }

    /**
     * @param array $arguments
     *
     * @return Definition
     */
    private static function getHandlerDefinition(array $arguments = [[]])
    {
        return $arguments[0]['handler'];
    }

    /**
     * @see \GuzzleHttp\choose_handler()
     *
     * @param ContainerBuilder $container
     *
     * @return null|Definition
     */
    private static function newHandlerDefinition(ContainerBuilder $container)
    {
        $definition = null;
        if (function_exists('curl_multi_exec') && function_exists('curl_exec')) {
            if (!$container->hasDefinition('nab3a.guzzle.handler.curl_multi')) {
                $container->register('nab3a.guzzle.handler.curl_multi', CurlMultiHandler::class)->setPublic(false);
            }
            if (!$container->hasDefinition('nab3a.guzzle.handler.curl')) {
                $container->register('nab3a.guzzle.handler.curl', CurlHandler::class)->setPublic(false);
            }
            $definition = new Definition(\Closure::class, [new Reference('nab3a.guzzle.handler.curl_multi'), new Reference('nab3a.guzzle.handler.curl')]);
            $definition->setFactory([Proxy::class, 'wrapSync']);
        } elseif (function_exists('curl_exec')) {
            $definition = $container->hasDefinition('nab3a.guzzle.handler.curl')
              ? $container->getDefinition('nab3a.guzzle.handler.curl')
              : $container->register('nab3a.guzzle.handler.curl', CurlHandler::class)->setPublic(false);
        } elseif (function_exists('curl_multi_exec')) {
            $definition = $container->hasDefinition('nab3a.guzzle.handler.curl_multi')
              ? $container->getDefinition('nab3a.guzzle.handler.curl_multi')
              : $container->register('nab3a.guzzle.handler.curl_multi', CurlMultiHandler::class)->setPublic(false);
        }

        if (ini_get('allow_url_fopen')) {
            if (!$container->hasDefinition('nab3a.guzzle.handler.stream')) {
                $container->register('nab3a.guzzle.handler.stream', StreamHandler::class)->setPublic(false);
            }
            $definition = $definition
              ? new Definition(\Closure::class, [$definition, new Reference('nab3a.guzzle.handler.stream')])
              : $container->getDefinition('nab3a.guzzle.handler.stream');
            $definition->setFactory([Proxy::class, 'wrapStreaming']);
        } elseif (!$definition) {
            // @todo find the right place to say this.
            throw new RuntimeException('GuzzleHttp requires cURL, the '
              .'allow_url_fopen ini setting, or a custom HTTP handler.');
        }

        return $definition;
    }
}
