<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory;

use M6Web\Bundle\AmqpBundle\Factory\ConsumerFactory as Base;

/**
 * ConsumerFactory.
 */
class ConsumerFactory extends \atoum
{
    public function testConstruct(): void
    {
        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($queueClass = '\AMQPQueue')
            ->and($exchangeClass = '\AMQPExchange')
                ->object($factory = new Base($channelClass, $queueClass, $exchangeClass))
                    ->isInstanceOf(Base::class);

        $this
            ->if($channelClass = '\DateTime')
            ->and($queueClass = '\AMQPQueue')
            ->and($exchangeClass = '\AMQPExchange')
                ->exception(
                    function () use ($channelClass, $queueClass, $exchangeClass): void {
                        $factory = new Base($channelClass, $queueClass, $exchangeClass);
                    },
                )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("channelClass '\DateTime' doesn't exist or not a AMQPChannel");

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($queueClass = '\DateTime')
            ->and($exchangeClass = '\AMQPExchange')
                ->exception(
                    function () use ($channelClass, $queueClass, $exchangeClass): void {
                        $factory = new Base($channelClass, $queueClass, $exchangeClass);
                    },
                )
                     ->isInstanceOf('InvalidArgumentException')
                     ->hasMessage("exchangeClass '\DateTime' doesn't exist or not a AMQPQueue");

        $this
            ->if($channelClass = '\AMQPChannel')
            ->and($queueClass = '\AMQPQueue')
            ->and($exchangeClass = '\DateTime')
            ->exception(
                function () use ($channelClass, $queueClass, $exchangeClass): void {
                    $factory = new Base($channelClass, $queueClass, $exchangeClass);
                },
            )
            ->isInstanceOf('InvalidArgumentException')
            ->hasMessage("exchangeClass '\DateTime' doesn't exist or not a AMQPExchange");
    }

    public function testFactory(): void
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $connexion = new \mock\AMQPConnection();

        $this
            ->if($channelClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPChannel')
            ->and($queueClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPQueue')
            ->and($exchangeClass = '\mock\M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock\MockAMQPExchange')
            ->and($consumerClass = '\mock\M6Web\Bundle\AmqpBundle\Amqp\Consumer')
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
                'arguments' => [],
                'passive' => false,
                'durable' => true,
                'exclusive' => true,
                'auto_delete' => false,
                'routing_keys' => ['key'],
            ])
            ->and($qosOptions = [
                'prefetch_size' => 0,
                'prefetch_count' => 0,
            ])
            ->and($factory = new Base($channelClass, $queueClass, $exchangeClass))
                ->object($factory->get($consumerClass, $connexion, $exchangeOptions, $queueOptions, false, $qosOptions))
                    ->isInstanceOf($consumerClass);
    }
}
