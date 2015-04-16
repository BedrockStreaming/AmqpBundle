<?php

namespace M6Web\Bundle\AmqpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class M6WebAmqpExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('data_collector.yml');
        }

        $this->loadConnections($container, $config);
        $this->loadProducers($container, $config);
        $this->loadConsumers($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function loadConnections(ContainerBuilder $container, array $config)
    {
        foreach ($config['connections'] as $key => $connection) {
            $connexionDefinition = new Definition($connection['class']);
            $connexionDefinition->addMethodCall('setHost ', [$connection['host']])
                                ->addMethodCall('setPort', [$connection['port']])
                                ->addMethodCall('setReadTimeout', [$connection['timeout']])
                                ->addMethodCall('setLogin', [$connection['login']])
                                ->addMethodCall('setPassword', [$connection['password']])
                                ->addMethodCall('setVhost', [$connection['vhost']]);

            if (!$connection['lazy']) {
                $connexionDefinition->addMethodCall('connect');
            }

            $container->setDefinition(
                sprintf('m6_web_amqp.connection.%s', $key),
                $connexionDefinition
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function loadProducers(ContainerBuilder $container, array $config)
    {
        $eventDispatcher = $config['event_dispatcher'];

        foreach ($config['producers'] as $key => $producer) {
            $lazy = $config['connections'][$producer['connection']]['lazy'];

            // Create the producer with the factory
            $producerDefinition = new Definition(
                $producer['class'],
                [
                    $producer['class'],
                    new Reference(sprintf('m6_web_amqp.connection.%s', $producer['connection'])),
                    $producer['exchange_options'],
                    $lazy,
                ]
            );

            // Add the Event dispatcher & Command Event
            if ($eventDispatcher === true) {
                $producerDefinition->addMethodCall(
                    'setEventDispatcher',
                    [
                        new Reference('event_dispatcher'),
                        $container->getParameter('m6_web_amqp.event.command.class')
                    ]
                );
            }

            // Use a factory to build the producer
            $producerDefinition->setFactoryService('m6_web_amqp.producer_factory')
                               ->setFactoryMethod('get');

            if ($lazy) {
                if (!method_exists($producerDefinition, 'setLazy')) {
                    throw new \InvalidArgumentException('It\'s not possible to declare a service as lazy. Are you using Symfony 2.3?');
                }

                $producerDefinition->setLazy(true);
            }

            $container->setDefinition(
                sprintf('m6_web_amqp.producer.%s', $key),
                $producerDefinition
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function loadConsumers(ContainerBuilder $container, array $config)
    {
        $eventDispatcher = $config['event_dispatcher'];

        foreach ($config['consumers'] as $key => $consumer) {
            $lazy = $config['connections'][$consumer['connection']]['lazy'];

            // Create the consumer with the factory
            $consumerDefinition = new Definition(
                $consumer['class'],
                [
                    $consumer['class'],
                    new Reference(sprintf('m6_web_amqp.connection.%s', $consumer['connection'])),
                    $consumer['exchange_options'],
                    $consumer['queue_options'],
                    $lazy,
                ]
            );

            // Add the Event dispatcher & Command Event
            if ($eventDispatcher === true) {
                $consumerDefinition->addMethodCall(
                    'setEventDispatcher',
                    [
                        new Reference('event_dispatcher'),
                        $container->getParameter('m6_web_amqp.event.command.class')
                    ]
                );
            }

            // Use a factory to build the consumer
            $consumerDefinition->setFactoryService('m6_web_amqp.consumer_factory')
                               ->setFactoryMethod('get');

            if ($lazy) {
                if (!method_exists($consumerDefinition, 'setLazy')) {
                    throw new \InvalidArgumentException('It\'s not possible to declare a service as lazy. Are you using Symfony 2.3?');
                }

                $consumerDefinition->setLazy(true);
            }

            $container->setDefinition(
                sprintf('m6_web_amqp.consumer.%s', $key),
                $consumerDefinition
            );
        }
    }

}
