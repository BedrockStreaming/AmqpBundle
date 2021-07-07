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

    public function qos($prefetchSize, $prefetchCount, $global = NULL)
    {
    }
}
