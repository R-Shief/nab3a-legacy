<?php

namespace Nab3aBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AttachPluginsCompilerPass implements CompilerPassInterface
{
    /**
     * @var
     */
    private $configuratorService;

    /**
     * @var
     */
    private $pluginTag;

    public function __construct($configuratorService, $pluginTag)
    {
        $this->configuratorService = $configuratorService;
        $this->pluginTag = $pluginTag;
    }

    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds($this->pluginTag);

        $configurators = [];
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $emitterId = $tag['id'];
                $configurators[$emitterId][] = $serviceId;
            }
        }

        foreach ($configurators as $forServiceId => $pluginIds) {
            $emitterDefinition = $container->findDefinition($forServiceId);

            if (!$emitterDefinition->getConfigurator()) {
                $configuratorDefinition = new Definition($this->configuratorService, [
                  array_map(function ($id) {
                      return new Reference($id);
                  }, $pluginIds),
                ]);
                $emitterDefinition->setConfigurator([$configuratorDefinition, 'configure']);
            }
        }
    }
}
