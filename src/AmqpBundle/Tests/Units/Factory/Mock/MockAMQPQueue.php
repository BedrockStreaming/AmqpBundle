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

    public function bind($exchange_name, $routing_key = null, $arguments = array())
    {
    }

    public function declareQueue()
    {
    }
}
