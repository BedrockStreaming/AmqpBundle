<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Purge queue event.
 */
class PurgeEvent extends Event
{
    public const NAME = 'amqp.purge';

    public function __construct(private readonly \AMQPQueue $queue)
    {
    }

    public function getQueue(): \AMQPQueue
    {
        return $this->queue;
    }
}
