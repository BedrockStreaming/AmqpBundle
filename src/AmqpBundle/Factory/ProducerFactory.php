<?php

namespace M6Web\Bundle\AmqpBundle\Factory;

/**
 * ProducerFactory
 */
class ProducerFactory
{
    /**
     * @var string
     */
    protected $channelClass;

    /**
     * @var string
     */
    protected $exchangeClass;

    /**
     * @var amqp channel
     */
    protected $channel;

    /**
     * __construct
     *
     * @param string $channelClass  Channel class name
     * @param string $exchangeClass Exchange class name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($channelClass, $exchangeClass)
    {
        if (!class_exists($channelClass) || !is_a($channelClass, 'AMQPChannel', true)) {
            throw new \InvalidArgumentException(
                sprintf("channelClass '%s' doesn't exist or not a AMQPChannel", $channelClass)
            );
        }

        if (!class_exists($exchangeClass) || !is_a($exchangeClass, 'AMQPExchange', true)) {
            throw new \InvalidArgumentException(
                sprintf("exchangeClass '%s' doesn't exist or not a AMQPExchange", $exchangeClass)
            );
        }

        $this->channelClass  = $channelClass;
        $this->exchangeClass = $exchangeClass;
    }

    /**
     * build the producer class
     *
     * @param string $class           Provider class name
     * @param string $connexion       AMQP connexion
     * @param array  $exchangeOptions Exchange Options
     * @param bool   $lazy            Specifies if it should connect
     *
     * @return Producer
     */
    public function get($class, $connexion, array $exchangeOptions, $lazy = false)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf("Producer class '%s' doesn't exist", $class)
            );
        }

        if ($lazy) {
            if (!$connexion->isConnected()) {
                $connexion->connect();
            }
        }

        // Open a new channel
        $channel = new $this->channelClass($connexion);

        // Create and declare an exchange
        $exchange = new $this->exchangeClass($channel);
        $exchange->setName($exchangeOptions['name']);
        $exchange->setType($exchangeOptions['type']);
        $exchange->setFlags(
            ($exchangeOptions['passive'] ? AMQP_PASSIVE : AMQP_NOPARAM) |
            ($exchangeOptions['durable'] ? AMQP_DURABLE : AMQP_NOPARAM) |
            ($exchangeOptions['auto_delete'] ? AMQP_AUTODELETE : AMQP_NOPARAM)
        );
        $exchange->declareExchange();

        // Create the producer
        $producer = new $class($exchange, $exchangeOptions);

        return $producer;
    }
}
