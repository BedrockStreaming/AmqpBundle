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
     * @var AMQPEnvelope
     */
    private $envelope;

    /**
     * Constructor.
     *
     * @param AMQPEnvelope|null $envelope
     */
    public function __construct(AMQPEnvelope $envelope = null)
    {
        $this->envelope = $envelope;
    }

    /**
     * @return AMQPEnvelope
     */
    public function getEnvelope()
    {
        return $this->envelope;
    }

    /**
     * @param AMQPEnvelope $envelope
     */
    public function setEnvelope($envelope)
    {
        $this->envelope = $envelope;
    }
}
