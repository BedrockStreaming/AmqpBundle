<?php

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory;

use atoum;
use M6Web\Bundle\AmqpBundle\Factory\ProducerFactory as Base;

/**
 * ProducerFactory
 */
class ProducerFactory extends atoum
{
    public function testConstruct()
    {

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($exchangeClass = '\AMQPExchange')
            ->and($queueClass = '\AMQPQueue')
                ->object($factory = new Base($channelClass, $exchangeClass, $queueClass))
                    ->isInstanceOf('M6Web\Bundle\AmqpBundle\Factory\ProducerFactory');

        $this
            ->if($channelClass = '\DateTime')
            ->and($exchangeClass = '\AMQPExchange')
            ->and($queueClass = '\AMQPQueue')
                ->exception(
                     function() use($channelClass, $exchangeClass, $queueClass) {
                         $factory = new Base($channelClass, $exchangeClass, $queueClass);
                     }
                 )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("channelClass '\DateTime' doesn't exist or not a AMQPChannel");

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($exchangeClass = '\DateTime')
            ->and($queueClass = '\AMQPQueue')
                ->exception(
                     function() use($channelClass, $exchangeClass, $queueClass) {
                         $factory = new Base($channelClass, $exchangeClass, $queueClass);
                     }
                 )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("exchangeClass '\DateTime' doesn't exist or not a AMQPExchange");

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($exchangeClass = '\AMQPExchange')
            ->and($queueClass = '\DateTime')
            ->exception(
                function() use($channelClass, $exchangeClass, $queueClass) {
                    $factory = new Base($channelClass, $exchangeClass, $queueClass);
                }
            )
            ->isInstanceOf('InvalidArgumentException')
            ->hasMessage("queueClass '\DateTime' doesn't exist or not a AMQPQueue");
    }

    public function testFactory()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $connexion = new \mock\AMQPConnection();

        $this
            ->if($channelClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPChannel')
            ->and($exchangeClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPExchange')
            ->and($producerClass = '\mock\M6Web\Bundle\AmqpBundle\Amqp\Producer')
            ->and($queueClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPQueue')
            ->and($exchangeOptions = [
                'name' => 'myexchange',
                'type' => 'type',
                'passive' => false,
                'durable' => true,
                'auto_delete' => false,
                'routing_keys' => ['key'],
                'arguments' => ['alternate-exchange' => 'my-ae'],
            ])
            ->and($queueOptions = [
                'name' => 'myqueue',
                'passive' => false,
                'durable' => true,
                'auto_delete' => false,
            ])
            ->and($factory = new Base($channelClass, $exchangeClass, $queueClass))
                ->object($factory->get($producerClass, $connexion, $exchangeOptions, $queueOptions))
                    ->isInstanceOf($producerClass);
    }

}
