<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Sandbox;

/**
 * Channel which does not do anything with connection.
 */
class NullChannel extends \AMQPChannel
{
    public function __construct(\AMQPConnection $amqp_connection)
    {
        // noop
    }
}
