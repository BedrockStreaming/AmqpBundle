<?php

namespace M6Web\Bundle\AmqpBundle\Sandbox;

/**
 * Connection that does not connect to anything.
 */
class NullConnection extends \AMQPConnection
{
    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function pconnect()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function pdisconnect()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function reconnect()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function preconnect()
    {
        return true;
    }
}
