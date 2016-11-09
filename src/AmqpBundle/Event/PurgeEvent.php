<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * Purge queue event
 */
class PurgeEvent extends SymfonyEvent
{
    const NAME = 'amqp.purge';

    /**
     * @var \AMQPQueue
     */
    private $queue;

    /**
     * Constructor
     *
     * @param \AMQPQueue $queue
     */
    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return \AMQPQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }
}
