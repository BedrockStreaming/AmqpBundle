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
     * @param string $channelClass Channel class name
     * @param string $queueClass   Queue class name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($channelClass, $queueClass)
    {
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

        $this->channelClass  = $channelClass;
        $this->queueClass = $queueClass;
    }

    /**
     * build the consumer class
     *
     * @param string $class           Consumer class name
     * @param string $connexion       AMQP connexion
     * @param array  $exchangeOptions Exchange Options
     * @param array  $queueOptions    Queue Options
     *
     * @return Consumer
     */
    public function get($class, $connexion, array $exchangeOptions, array $queueOptions)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf("Consumer class '%s' doesn't exist", $class)
            );
        }

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
        $consumer = new $class($queue, $queueOptions);

        return $consumer;
    }
}
