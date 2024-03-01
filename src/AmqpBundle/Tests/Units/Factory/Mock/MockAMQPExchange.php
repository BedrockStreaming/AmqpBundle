<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Factory\Mock;

/**
 * MockAMQPExchange.
 */
class MockAMQPExchange extends \AMQPExchange
{
    public function __construct(\AMQPChannel $amqp_channel)
    {
    }

    public function declareExchange(): void
    {
    }
}
