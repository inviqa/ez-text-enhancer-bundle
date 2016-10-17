<?php

namespace Inviqa\EzTextEnhancerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TextEnhancerCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('inviqa.text_enhancer.chain_text_enhancer')) {
            return;
        }

        $chainTextEnhancerDefinition = $container->getDefinition('inviqa.text_enhancer.chain_text_enhancer');
        foreach ($this->findTextEnhancerServices($container) as $id) {
            $chainTextEnhancerDefinition->addMethodCall('addTextEnhancer', [new Reference($id)]);
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return string[]
     */
    private function findTextEnhancerServices(ContainerBuilder $container)
    {
        $services = [];
        foreach ($container->findTaggedServiceIds('text.enhancer') as $id => $tags) {
            foreach ($tags as $attributes) {
                $services[] = ['id' => $id, 'priority' => (int) (@$attributes['priority'] ?: 0)];
            }
        }

        usort($services, function (array $item1, array $item2) {
            return $item2['priority'] - $item1['priority'];
        });

        return array_column($services, 'id');
    }
}
