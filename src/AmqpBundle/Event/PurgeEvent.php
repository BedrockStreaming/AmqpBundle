<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Purge queue event.
 */
class PurgeEvent extends Event
{
    const NAME = 'amqp.purge';

    private \AMQPQueue $queue;

    public function __construct(\AMQPQueue $queue)
    {
        $this->queue = $queue;
    }

    public function getQueue(): \AMQPQueue
    {
        return $this->queue;
    }
}
