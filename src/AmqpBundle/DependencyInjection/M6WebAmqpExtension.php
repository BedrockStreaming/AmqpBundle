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

        if (!$config['disable_data_collector']) {
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
                                ->addMethodCall('setVhost', [$connection['vhost']])
                                ->addMethodCall('connect');

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
        foreach ($config['producers'] as $key => $producer) {
            // Create the producer with the factory
            $producerDefinition = new Definition(
                $producer['class'],
                [
                    new Reference(sprintf('m6_web_amqp.connection.%s', $producer['connection'])),
                    $producer['exchange_options'],
                    $producer['queue_options'],
                ]
            );

            // Add the Event dispatcher & Command Event
            $producerDefinition->addMethodCall(
                'setEventDispatcher',
                [
                    new Reference('event_dispatcher'),
                    $container->getParameter('m6_web_amqp.event.command.class')
                ]
            );

            // Use a factory to build the producer
            $producerDefinition->setFactoryService('m6_web_amqp.producer_factory')
                               ->setFactoryMethod('get');

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
        foreach ($config['consumers'] as $key => $consumer) {
            // Create the consumer with the factory
            $consumerDefinition = new Definition(
                $consumer['class'],
                [
                    new Reference(sprintf('m6_web_amqp.connection.%s', $consumer['connection'])),
                    $consumer['exchange_options'],
                    $consumer['queue_options'],
                ]
            );

            // Add the Event dispatcher & Command Event
            $consumerDefinition->addMethodCall(
                'setEventDispatcher',
                [
                    new Reference('event_dispatcher'),
                    $container->getParameter('m6_web_amqp.event.command.class')
                ]
            );

            // Use a factory to build the consumer
            $consumerDefinition->setFactoryService('m6_web_amqp.consumer_factory')
                               ->setFactoryMethod('get');

            $container->setDefinition(
                sprintf('m6_web_amqp.consumer.%s', $key),
                $consumerDefinition
            );
        }
    }

}
