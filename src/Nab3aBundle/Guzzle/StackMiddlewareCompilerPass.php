<?php

namespace Nab3aBundle\Guzzle;

use GuzzleHttp\HandlerStack;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
        $configurators = [];
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $clientId = $tag['id'];
                $configurators[$clientId][] = $serviceId;
            }
        }

        foreach ($configurators as $clientId => $middlewareIds) {
            $clientDefinition = $container->getDefinition($clientId);
            $arguments = $clientDefinition->getArguments();

            $handlerDefinition = self::hasHandlerDefinition($arguments) ? self::getHandlerDefinition($arguments) : self::newHandlerDefinition();
            $configuratorDefinition = self::getConfiguratorDefinition($middlewareIds);

            $handlerDefinition->setConfigurator([$configuratorDefinition, 'configure']);
            $arguments[0]['handler'] = $handlerDefinition;

            $clientDefinition->setArguments($arguments);
            $container->setDefinition($clientId, $clientDefinition);
        }
    }

    private static function hasHandlerDefinition(array $arguments = [[]])
    {
        return isset($arguments[0]['handler']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private static function getHandlerDefinition(array $arguments = [[]])
    {
        return $arguments[0]['handler'];
    }

    private static function newHandlerDefinition()
    {
        $definition = new Definition(HandlerStack::class);
        $definition->setFactory([Configurator::class, 'create']);

        return $definition;
    }

    /**
     * @param $ids
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private static function getConfiguratorDefinition($ids)
    {
        $arguments = array_map(function ($id) {
            return new Reference($id);
        }, $ids);

        return new Definition(Configurator::class, [$arguments]);
    }
}
