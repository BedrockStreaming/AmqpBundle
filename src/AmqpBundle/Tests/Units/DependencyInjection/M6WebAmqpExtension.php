<?php

namespace M6Web\Bundle\AmqpBundle\Tests\Units\DependencyInjection;

use M6Web\Bundle\AmqpBundle\DependencyInjection\M6WebAmqpExtension as Base;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Parser;
use atoum;

/**
 * Class M6WebAmqpExtension.
 */
class M6WebAmqpExtension extends atoum
{
    /**
     * @return ContainerBuilder
     */
    protected function getContainerForConfiguration(string $fixtureName)
    {
        $extension = new Base();

        $parameterBag = new ParameterBag(array('kernel.debug' => true));
        $container = new ContainerBuilder($parameterBag);

        $parser = new Parser();
        $config = $parser->parseFile(__DIR__.'/../../Fixtures/'.$fixtureName.'.yml');

        $extension->load($config, $container);

        return $container;
    }

    public function testQueueArgumentsConfig()
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

        //test connection options
        $this
            ->boolean($container->hasDefinition('m6_web_amqp.connection.with_heartbeat'))
                ->isTrue()
            ->array($connectionArguments = $container->getDefinition('m6_web_amqp.connection.with_heartbeat')->getArguments())
                ->hasSize(1)
            ->integer($connectionArguments[0]['heartbeat'])
                ->isEqualTo(1);
    }

    public function testSandboxClasses()
    {
        $container = $this->getContainerForConfiguration('queue-arguments-config');

        $this
            ->string($container->getParameter('m6_web_amqp.exchange.class'))
                ->isEqualTo('M6Web\Bundle\AmqpBundle\Sandbox\NullExchange')
            ->string($container->getParameter('m6_web_amqp.queue.class'))
                ->isEqualTo('M6Web\Bundle\AmqpBundle\Sandbox\NullQueue')
            ->string($container->getParameter('m6_web_amqp.connection.class'))
                ->isEqualTo('M6Web\Bundle\AmqpBundle\Sandbox\NullConnection')
            ->string($container->getParameter('m6_web_amqp.channel.class'))
                ->isEqualTo('M6Web\Bundle\AmqpBundle\Sandbox\NullChannel')
            ->string($container->getParameter('m6_web_amqp.envelope.class'))
                ->isEqualTo('\M6Web\Bundle\AmqpBundle\Sandbox\NullEnvelope');
    }

    public function testDefaultConfiguration()
    {
        $container = $this->getContainerForConfiguration('queue-defaults');

        //sandbox is off by default, check indirectly via classes definition
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
