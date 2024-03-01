<?php

namespace M6Web\Bundle\AmqpBundle\Sandbox;

/**
 * Queue which does nothing.
 */
class NullQueue extends \AMQPQueue
{
    /**
     * List of envelopes to return.
     *
     * @var \AMQPEnvelope[]|\SplQueue
     */
    private $envelopes;

    /**
     * {@inheritdoc}
     */
    public function __construct(\AMQPChannel $channel)
    {
        $this->envelopes = new \SplQueue();
    }

    /**
     * Enqueue message or no message.
     *
     * @param \AMQPEnvelope|null $envelope
     */
    public function enqueue(\AMQPEnvelope $envelope = null): void
    {
        $this->envelopes->enqueue($envelope);
    }

    /**
     * {@inheritdoc}
     */
    public function get($flags = AMQP_NOPARAM): ?\AMQPEnvelope
    {
        if (!$this->envelopes->isEmpty()) {
            return $this->envelopes->dequeue();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function declareQueue(): int
    {
        return $this->envelopes->count();
    }
}
