<?php

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory;

use atoum;
use M6Web\Bundle\AmqpBundle\Factory\ConsumerFactory as Base;

/**
 * ConsumerFactory
 */
class ConsumerFactory extends atoum
{
    public function testConstruct()
    {

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($queueClass = '\AMQPQueue')
                ->object($factory = new Base($channelClass, $queueClass))
                    ->isInstanceOf('M6Web\Bundle\AmqpBundle\Factory\ConsumerFactory');

        $this
            ->if($channelClass = '\DateTime')
            ->and($queueClass = '\AMQPQueue')
                ->exception(
                     function() use($channelClass, $queueClass) {
                         $factory = new Base($channelClass, $queueClass);
                     }
                 )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("channelClass '\DateTime' doesn't exist or not a AMQPChannel");

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($queueClass = '\DateTime')
                ->exception(
                     function() use($channelClass, $queueClass) {
                         $factory = new Base($channelClass, $queueClass);
                     }
                 )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("exchangeClass '\DateTime' doesn't exist or not a AMQPQueue");
    }

    public function testFactory()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $connexion = new \mock\AMQPConnection();

        $this
            ->if($channelClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPChannel')
            ->and($queueClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPQueue')
            ->and($consumerClass = '\mock\M6Web\Bundle\AmqpBundle\Amqp\Consumer')
            ->and($exchangeOptions = ['name' => 'myexchange'])
            ->and($queueOptions = [
                'name' => 'myqueue',
                'arguments' => [],
                'passive' => false,
                'durable' => true,
                'exclusive' => true,
                'auto_delete' => false,
                'routing_keys' => ['key']
            ])
            ->and($factory = new Base($channelClass, $queueClass))
                ->object($factory->get($consumerClass, $connexion, $exchangeOptions, $queueOptions))
                    ->isInstanceOf($consumerClass);
    }

}
