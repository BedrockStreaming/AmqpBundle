<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use AMQPEnvelope;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Pre retrieve message event.
 */
class PreRetrieveEvent extends Event
{
    const NAME = 'amqp.pre_retrieve';

    private ?AMQPEnvelope $envelope;

    public function __construct(?AMQPEnvelope $envelope)
    {
        $this->envelope = $envelope;
    }

    public function getEnvelope(): ?AMQPEnvelope
    {
        return $this->envelope;
    }

    public function setEnvelope(?AMQPEnvelope $envelope)
    {
        $this->envelope = $envelope;
    }
}
