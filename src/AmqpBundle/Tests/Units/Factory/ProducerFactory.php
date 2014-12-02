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
                ->object($factory = new Base($channelClass, $exchangeClass))
                    ->isInstanceOf('M6Web\Bundle\AmqpBundle\Factory\ProducerFactory');

        $this
            ->if($channelClass = '\DateTime')
            ->and($exchangeClass = '\AMQPExchange')
                ->exception(
                     function() use($channelClass, $exchangeClass) {
                         $factory = new Base($channelClass, $exchangeClass);
                     }
                 )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("channelClass '\DateTime' doesn't exist or not a AMQPChannel");

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($exchangeClass = '\DateTime')
                ->exception(
                     function() use($channelClass, $exchangeClass) {
                         $factory = new Base($channelClass, $exchangeClass);
                     }
                 )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("exchangeClass '\DateTime' doesn't exist or not a AMQPExchange");
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
            ->and($exchangeOptions = [
                'name' => 'myexchange',
                'type' => 'type',
                'passive' => false,
                'durable' => true,
                'auto_delete' => false,
                'routing_keys' => ['key']
            ])
            ->and($factory = new Base($channelClass, $exchangeClass))
                ->object($factory->get($producerClass, $connexion, $exchangeOptions))
                    ->isInstanceOf($producerClass);
    }

}
