<?php

namespace M6Web\Bundle\AmqpBundle\Factory;

/**
 * ConsumerFactory
 */
class ConsumerFactory
{
    /**
     * @var string
     */
    protected $consumerClass;

    /**
     * @var string
     */
    protected $channelClass;

    /**
     * @var string
     */
    protected $queueClass;

    /**
     * @var amqp channel
     */
    protected $channel;

    /**
     * __construct
     *
     * @param string $consumerClass Consumer class name
     * @param string $channelClass  Channel class name
     * @param string $queueClass    Queue class name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($consumerClass, $channelClass, $queueClass)
    {
        if (!class_exists($consumerClass)) {
            throw new \InvalidArgumentException(
                sprintf("consumerClass '%s' doesn't exist", $consumerClass)
            );
        }

        if (!class_exists($channelClass) || !is_a($channelClass, "AMQPChannel", true)) {
            throw new \InvalidArgumentException(
                sprintf("channelClass '%s' doesn't exist or not a AMQPChannel", $channelClass)
            );
        }

        if (!class_exists($queueClass) || !is_a($queueClass, "AMQPQueue", true)) {
            throw new \InvalidArgumentException(
                sprintf("exchangeClass '%s' doesn't exist or not a AMQPQueue", $queueClass)
            );
        }

        $this->consumerClass  = $consumerClass;
        $this->channelClass  = $channelClass;
        $this->queueClass = $queueClass;
    }

    /**
     * build the consumer class
     *
     * @param string $connexion       AMQP connexion
     * @param array  $exchangeOptions Exchange Options
     * @param array  $queueOptions    Queue Options
     *
     * @return Consumer
     */
    public function get($connexion, array $exchangeOptions, array $queueOptions)
    {
        $params = array();

        // Open a new channel
        $channel = new $this->channelClass($connexion);

        // Create the queue
        $queue = new $this->queueClass($channel);
        $queue->setName($queueOptions['name']);
        $queue->setArguments($queueOptions['arguments']);
        $queue->setFlags(
            ($queueOptions['passive'] ? AMQP_PASSIVE : AMQP_NOPARAM) |
            ($queueOptions['durable'] ? AMQP_DURABLE : AMQP_NOPARAM) |
            ($queueOptions['exclusive'] ? AMQP_EXCLUSIVE : AMQP_NOPARAM) |
            ($queueOptions['auto_delete'] ? AMQP_AUTODELETE : AMQP_NOPARAM)
        );

        // Declare the queue
        $queue->declareQueue();

        // Bind the queue to some routing keys
        foreach ($queueOptions['routing_keys'] as $routingKey) {
            $queue->bind($exchangeOptions['name'], $routingKey);
        }

        // Create the consumer
        $consumer = new $this->consumerClass($queue, $queueOptions);

        return $consumer;
    }
}
