<?php

namespace Nab3aBundle\Guzzle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
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
                if (isset($tag['name'])) {
                    $name = $tag['name'];
                    $stacks[$clientId][$name] = $serviceId;
                } else {
                    $stacks[$clientId][] = $serviceId;
                }
            }
        }

        foreach ($stacks as $clientId => $middlewareIds) {
            $clientDefinition = $container->getDefinition($clientId);
            $arguments = $clientDefinition->getArguments();

            $handlerDefinition = self::hasHandlerDefinition($arguments) ? self::getHandlerDefinition($arguments) : self::newHandlerDefinition($container);

            $stackDefinition = new DefinitionDecorator('nab3a.guzzle.handler_stack');
            $stackDefinition->setArguments([$handlerDefinition]);
            foreach ($middlewareIds as $name => $middlewareId) {
                if (is_string($name)) {
                    $stackDefinition->addMethodCall('push', [new Reference($middlewareId), $name]);
                } else {
                    $stackDefinition->addMethodCall('push', [new Reference($middlewareId)]);
                }
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
        $definition = new Definition();
        $definition->setFactory('GuzzleHttp\choose_handler');

        return $definition;
    }
}
