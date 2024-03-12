<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Sandbox;

/**
 * Connection that does not connect to anything.
 */
class NullConnection extends \AMQPConnection
{
    /**
     * {@inheritdoc}
     */
    public function connect(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function pconnect(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function pdisconnect(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function reconnect(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preconnect(): void
    {
    }
}
