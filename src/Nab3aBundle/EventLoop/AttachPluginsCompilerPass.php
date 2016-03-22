<?php

namespace Nab3aBundle\EventLoop;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class AttachPluginsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('event_loop.plugin');

        $configurators = [];
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $emitterId = $tag['id'];
                $configurators[$emitterId][] = $serviceId;
            }
        }

        foreach ($configurators as $forServiceId => $pluginIds) {
            $emitterDefinition = $container->getDefinition($forServiceId);

            if (!$emitterDefinition->getConfigurator()) {
                $configuratorDefinition = new DefinitionDecorator('nab3a.event_loop.configurator');
                $configuratorDefinition->setArguments([
                    array_map(function ($id) {
                        return new Reference($id);
                    }, $pluginIds),
                ]);
                $emitterDefinition->setConfigurator([$configuratorDefinition, 'configure']);
            }
        }
    }
}
