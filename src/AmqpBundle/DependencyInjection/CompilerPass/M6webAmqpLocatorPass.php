<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class M6webAmqpLocatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('m6_web_amqp.locator')) {
            return;
        }
        $locator = $container->getDefinition('m6_web_amqp.locator');
        $consumers = [];
        $producers = [];

        $taggedServices = $container->findTaggedServiceIds('m6_web_amqp.consumers');
        foreach ($taggedServices as $id => $taggedService) {
            $consumers[$id] = new Reference($id);
        }
        $locator->addMethodCall('setConsumers', [$consumers]);

        $taggedServices = $container->findTaggedServiceIds('m6_web_amqp.producers');
        foreach ($taggedServices as $id => $taggedService) {
            $producers[$id] = new Reference($id);
        }
        $locator->addMethodCall('setProducers', [$producers]);
    }
}
