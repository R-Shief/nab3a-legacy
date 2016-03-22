<?php

namespace Nab3aBundle\Evenement;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class AttachPluginsCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('evenement.plugin');

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
                $configuratorDefinition = new DefinitionDecorator('nab3a.evenement.configurator');
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
