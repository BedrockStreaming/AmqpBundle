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
    protected $producerClass;

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
     * @param string $producerClass Producer class name
     * @param string $channelClass  Channel class name
     * @param string $exchangeClass Exchange class name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($producerClass, $channelClass, $exchangeClass)
    {
        if (!class_exists($producerClass)) {
            throw new \InvalidArgumentException(
                sprintf("producerClass '%s' doesn't exist", $producerClass)
            );
        }

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

        $this->producerClass = $producerClass;
        $this->channelClass  = $channelClass;
        $this->exchangeClass = $exchangeClass;
    }

    /**
     * build the producer class
     *
     * @param string $connexion       AMQP connexion
     * @param array  $exchangeOptions Exchange Options
     * @param array  $queueOptions    Queue Options
     *
     * @return Producer
     */
    public function get($connexion, array $exchangeOptions, array $queueOptions)
    {
        $params = array();

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
        $producer = new $this->producerClass($exchange, $queueOptions);

        return $producer;
    }
}
