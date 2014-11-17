<?php

namespace M6Web\Bundle\AmqpBundle\Amqp;

/**
 * Producer
 */
class Producer extends AbstractAmqp
{
    /**
     * @var \AMQPExchange
     */
    protected $exchange = null;

    /**
     * @var array
     */
    protected $queueOptions = [];

    /**
     * __construct
     *
     * @param string $exchange     Amqp Exchange
     * @param array  $queueOptions Queue options
     */
    public function __construct(\AMQPExchange $exchange, Array $queueOptions)
    {
        $this->exchange     = $exchange;
        $this->queueOptions = $queueOptions;
    }

    /**
     * @param string $message    The message to publish.
     * @param string $flags      One or more of AMQP_MANDATORY and AMQP_IMMEDIATE.
     * @param array  $attributes One of content_type, content_encoding,
     *                           message_id, user_id, app_id, delivery_mode, priority,
     *                           timestamp, expiration, type or reply_to.
     *
     * @return boolean          TRUE on success or FALSE on failure.
     *
     * @throws AMQPExchangeException On failure.
     * @throws AMQPChannelException If the channel is not open.
     * @throws AMQPConnectionException If the connection to the broker was lost.
     */
    public function sendMessage($message, $flags = AMQP_NOPARAM, array $attributes = array())
    {
        $success = true;
        foreach ($this->queueOptions['routing_keys'] as $routingKey) {
            $success &= $this->call($this->exchange, 'publish', [$message, $routingKey, $flags, $attributes]);
        }

        return $success;
    }

    /**
     * @return AMQPExchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param \AMQPExchange $exchange
     *
     * @return \M6Web\Bundle\AmqpBundle\Amqp\Consumer
     */
    public function setExchange(\AMQPExchange $exchange)
    {
        $this->exchange = $exchange;

        return $this;
    }
}