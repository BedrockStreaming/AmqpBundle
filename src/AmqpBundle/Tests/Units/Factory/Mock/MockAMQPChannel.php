<?php

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock;

/**
 * MockAMQPChannel.
 */
class MockAMQPChannel extends \AMQPChannel
{
    public function __construct(\AMQPConnection $amqp_connection)
    {
    }

    public function qos(int $size, int $count, bool $global = NULL): void
    {
    }

    public function setPrefetchSize(int $size): void
    {
    }

    public function setPrefetchCount(int $count): void
    {
    }
}
