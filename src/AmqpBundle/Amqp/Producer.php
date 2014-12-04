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
    protected $exchangeOptions = [];

    /**
     * __construct
     *
     * @param \AMQPExchange $exchange        Amqp Exchange
     * @param array         $exchangeOptions Exchange options
     */
    public function __construct(\AMQPExchange $exchange, Array $exchangeOptions)
    {
        $this->setExchange($exchange);
        $this->setExchangeOptions($exchangeOptions);
    }

    /**
     * @param string $message    The message to publish.
     * @param int    $flags      One or more of AMQP_MANDATORY and AMQP_IMMEDIATE.
     * @param array  $attributes One of content_type, content_encoding,
     *                           message_id, user_id, app_id, delivery_mode, priority,
     *                           timestamp, expiration, type or reply_to.
     *
     * @return boolean          TRUE on success or FALSE on failure.
     *
     * @throws \AMQPExchangeException On failure.
     * @throws \AMQPChannelException If the channel is not open.
     * @throws \AMQPConnectionException If the connection to the broker was lost.
     */
    public function publishMessage($message, $flags = AMQP_NOPARAM, array $attributes = [])
    {
        // Merge attributes
        $attributes = empty($attributes) ? $this->exchangeOptions['publish_attributes'] :
                      (empty($this->exchangeOptions['publish_attributes']) ? $attributes :
                      array_merge($this->exchangeOptions['publish_attributes'], $attributes));

        // Publish the message for each routing keys
        $success = true;
        foreach ($this->exchangeOptions['routing_keys'] as $routingKey) {
            $success &= $this->call($this->exchange, 'publish', [$message, $routingKey, $flags, $attributes]);
        }

        return (boolean) $success;
    }

    /**
     * @return \AMQPExchange
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

    /**
     * @return array
     */
    public function getExchangeOptions()
    {
        return $this->exchangeOptions;
    }

    /**
     * @param Array $exchangeOptions
     *
     * @return \M6Web\Bundle\AmqpBundle\Amqp\Consumer
     */
    public function setExchangeOptions(Array $exchangeOptions)
    {
        $this->exchangeOptions = $exchangeOptions;

        if (!array_key_exists('publish_attributes', $this->exchangeOptions)) {
            $this->exchangeOptions['publish_attributes'] = [];
        }

        if (!array_key_exists('routing_keys', $this->exchangeOptions)) {
            $this->exchangeOptions['routing_keys'] = [];
        }

        return $this;
    }
}
