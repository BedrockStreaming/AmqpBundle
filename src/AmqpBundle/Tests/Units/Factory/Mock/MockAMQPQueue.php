<?php

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock;

/**
 * MockAMQPQueue.
 */
class MockAMQPQueue extends \AMQPQueue
{
    public function __construct(\AMQPChannel $amqp_channel)
    {
    }

    public function bind(string $exchange_name, string $routing_key = null, array $arguments = []): void
    {
    }

    public function declareQueue(): int
    {
        return 1;
    }
}
