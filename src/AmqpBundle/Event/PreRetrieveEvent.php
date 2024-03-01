<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use AMQPEnvelope;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Pre retrieve message event.
 */
class PreRetrieveEvent extends Event
{
    public const NAME = 'amqp.pre_retrieve';

    public function __construct(private ?AMQPEnvelope $envelope)
    {
    }

    public function getEnvelope(): ?AMQPEnvelope
    {
        return $this->envelope;
    }

    public function setEnvelope(?AMQPEnvelope $envelope): void
    {
        $this->envelope = $envelope;
    }
}
