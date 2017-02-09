<?php

namespace M6Web\Bundle\AmqpBundle\Sandbox;

use AMQPConnection;

/**
 * Channel which does not do anything with connection.
 */
class NullChannel extends \AMQPChannel
{
    public function __construct(AMQPConnection $amqp_connection)
    {
        //noop
    }
}
