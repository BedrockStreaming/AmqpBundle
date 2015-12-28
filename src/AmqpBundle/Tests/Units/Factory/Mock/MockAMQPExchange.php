<?php

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock;

/**
 * MockAMQPExchange
 */
class MockAMQPExchange extends \AMQPExchange
{
    public function __construct(\AMQPChannel $amqp_channel)
    {

    }

    public function declareExchange()
    {
    }
}