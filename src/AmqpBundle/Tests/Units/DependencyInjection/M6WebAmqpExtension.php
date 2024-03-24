<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Tests\Units\DependencyInjection;

use M6Web\Bundle\AmqpBundle\DependencyInjection\M6WebAmqpExtension as Base;
use M6Web\Bundle\AmqpBundle\Sandbox\NullChannel;
use M6Web\Bundle\AmqpBundle\Sandbox\NullConnection;
use M6Web\Bundle\AmqpBundle\Sandbox\NullEnvelope;
use M6Web\Bundle\AmqpBundle\Sandbox\NullExchange;
use M6Web\Bundle\AmqpBundle\Sandbox\NullQueue;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Parser;

/**
 * Class M6WebAmqpExtension.
 */
class M6WebAmqpExtension extends \atoum
{
    protected function getContainerForConfiguration(string $fixtureName): ContainerBuilder
    {
        $extension = new Base();

        $parameterBag = new ParameterBag(['kernel.debug' => true]);
        $container = new ContainerBuilder($parameterBag);

        $parser = new Parser();
        $config = $parser->parseFile(__DIR__.'/../../Fixtures/'.$fixtureName.'.yml');

        $extension->load($config, $container);

        return $container;
    }

    public function testQueueArgumentsConfig(): void
    {
        $container = $this->getContainerForConfiguration('queue-arguments-config');

        // test producer queue options
        $this
            ->boolean($container->hasDefinition('m6_web_amqp.producer.producer_2'))
                ->isTrue()
            ->boolean($container->getDefinition('m6_web_amqp.producer.producer_2')->isShared())
                ->isFalse()
            ->array($queueOptions = $container->getDefinition('m6_web_amqp.producer.producer_2')->getArgument(3))
                ->hasSize(6)
            ->string($queueOptions['name'])
                ->isEqualTo('ha.queue_exchange_2')
            ->array($arguments = $queueOptions['arguments'])
                ->hasSize(1)
                ->hasKey('x-message-ttl')
                ->contains(1000)
            ->boolean($queueOptions['passive'])
                ->isFalse()
            ->boolean($queueOptions['durable'])
                ->isTrue()
            ->boolean($queueOptions['auto_delete'])
                ->isFalse()
            ->array($queueOptions['routing_keys'])
                ->isEmpty()
        ;

        // test consumer queue options
        $this
            ->boolean($container->hasDefinition('m6_web_amqp.consumer.consumer_1'))
                ->isTrue()
            ->boolean($container->getDefinition('m6_web_amqp.consumer.consumer_1')->isShared())
                ->isFalse()
            ->array($queueOptions = $container->getDefinition('m6_web_amqp.consumer.consumer_1')->getArgument(3))
                ->hasSize(7)
            ->string($queueOptions['name'])
                ->isEqualTo('ha.queue_exchange_1')
            ->array($arguments = $queueOptions['arguments'])
                ->hasSize(1)
                ->hasKey('x-dead-letter-exchange')
                ->contains('exchange_2')
            ->boolean($queueOptions['passive'])
                ->isFalse()
            ->boolean($queueOptions['durable'])
                ->isTrue()
            ->boolean($queueOptions['auto_delete'])
                ->isFalse()
            ->array($queueOptions['routing_keys'])
                ->hasSize(1)
                ->contains('super_routing_key')
        ;

        // test connection options
        $this
            ->boolean($container->hasDefinition('m6_web_amqp.connection.with_heartbeat'))
                ->isTrue()
            ->array($connectionArguments = $container->getDefinition('m6_web_amqp.connection.with_heartbeat')->getArguments())
                ->hasSize(1)
            ->integer($connectionArguments[0]['heartbeat'])
                ->isEqualTo(1);
        $this
            ->boolean($container->has('m6_web_amqp.connection.with_read_timeout_and_timeout'))
                ->isTrue()
            ->array($connectionArguments = $container->getDefinition('m6_web_amqp.connection.with_read_timeout_and_timeout')->getArguments())
                ->hasSize(1)
            ->integer($connectionArguments[0]['read_timeout'])
                ->isEqualTo(100)
            ->integer($connectionArguments[0]['write_timeout'])
                ->isEqualTo(50);

        $this
            ->boolean($container->has('m6_web_amqp.connection.with_read_timeout_only'))
                ->isTrue()
            ->array($connectionArguments = $container->getDefinition('m6_web_amqp.connection.with_read_timeout_only')->getArguments())
                ->hasSize(1)
            ->integer($connectionArguments[0]['read_timeout'])
                ->isEqualTo(100);
    }

    public function testSandboxClasses(): void
    {
        $container = $this->getContainerForConfiguration('queue-arguments-config');

        $this
            ->string($container->getParameter('m6_web_amqp.exchange.class'))
                ->isEqualTo(NullExchange::class)
            ->string($container->getParameter('m6_web_amqp.queue.class'))
                ->isEqualTo(NullQueue::class)
            ->string($container->getParameter('m6_web_amqp.connection.class'))
                ->isEqualTo(NullConnection::class)
            ->string($container->getParameter('m6_web_amqp.channel.class'))
                ->isEqualTo(NullChannel::class)
            ->string($container->getParameter('m6_web_amqp.envelope.class'))
                ->isEqualTo(NullEnvelope::class);
    }

    public function testDefaultConfiguration(): void
    {
        $container = $this->getContainerForConfiguration('queue-defaults');

        // sandbox is off by default, check indirectly via classes definition
        $this
            ->string($container->getParameter('m6_web_amqp.exchange.class'))
                ->isEqualTo('AMQPExchange')
            ->string($container->getParameter('m6_web_amqp.queue.class'))
                ->isEqualTo('AMQPQueue')
            ->string($container->getParameter('m6_web_amqp.connection.class'))
                ->isEqualTo('AMQPConnection')
            ->string($container->getParameter('m6_web_amqp.channel.class'))
                ->isEqualTo('AMQPChannel')
            ->string($container->getParameter('m6_web_amqp.envelope.class'))
                ->isEqualTo('AMQPEnvelope');
    }
}
