<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock;

/**
 * MockAMQPChannel.
 */
class MockAMQPChannel extends \AMQPChannel
{
    public function __construct(\AMQPConnection $amqp_connection)
    {
    }

    public function qos(int $size, int $count, ?bool $global = null): void
    {
    }

    public function setPrefetchSize(int $size): void
    {
    }

    public function setPrefetchCount(int $count): void
    {
    }
}
