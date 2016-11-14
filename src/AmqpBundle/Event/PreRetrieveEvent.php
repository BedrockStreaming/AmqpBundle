<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use AMQPEnvelope;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * Pre retrieve message event
 */
class PreRetrieveEvent extends SymfonyEvent
{
    const NAME = 'amqp.pre_retrieve';

    /**
     * @var AMQPEnvelope|bool
     */
    private $envelope;

    /**
     * Constructor.
     *
     * @param AMQPEnvelope|bool $envelope
     */
    public function __construct($envelope)
    {
        $this->envelope = $envelope;
    }

    /**
     * @return AMQPEnvelope|bool
     */
    public function getEnvelope()
    {
        return $this->envelope;
    }

    /**
     * @param AMQPEnvelope|bool $envelope
     */
    public function setEnvelope($envelope)
    {
        $this->envelope = $envelope;
    }
}
