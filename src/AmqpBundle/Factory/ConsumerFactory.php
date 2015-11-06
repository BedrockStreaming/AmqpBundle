<?php

namespace M6Web\Bundle\AmqpBundle\Factory;

use M6Web\Bundle\AmqpBundle\Amqp\Consumer;

/**
 * ConsumerFactory
 */
class ConsumerFactory extends AMQPFactory
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
     * @var string
     */
    protected $exchangeClass;

    /**
     * __construct
     *
     * @param string $channelClass  Channel class name
     * @param string $queueClass    Queue class name
     * @param string $exchangeClass Exchange class name
     */
    public function __construct($channelClass, $queueClass, $exchangeClass)
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

        if (!class_exists($exchangeClass) || !is_a($exchangeClass, "AMQPExchange", true)) {
            throw new \InvalidArgumentException(
                sprintf("exchangeClass '%s' doesn't exist or not a AMQPExchange", $exchangeClass)
            );
        }

        $this->channelClass  = $channelClass;
        $this->queueClass = $queueClass;
        $this->exchangeClass = $exchangeClass;
    }

    /**
     * build the consumer class
     *
     * @param string                 $class           Consumer class name
     * @param \AMQPConnection        $connexion       AMQP connexion
     * @param array                  $exchangeOptions Exchange Options
     * @param array                  $queueOptions    Queue Options
     * @param bool                   $lazy            Specifies if it should connect
     * @param array                  $qosOptions      Qos Options
     *
     * @return Consumer
     */
    public function get($class, $connexion, array $exchangeOptions, array $queueOptions, $lazy = false, array $qosOptions = [])
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf("Consumer class '%s' doesn't exist", $class)
            );
        }

        if ($lazy) {
            if (!$connexion->isConnected()) {
                $connexion->connect();
            }
        }

        /** @var \AMQPChannel $channel */
        $channel = new $this->channelClass($connexion);

        if (isset($qosOptions['prefetch_size'])) {
            $channel->setPrefetchSize($qosOptions['prefetch_size']);
        }
        if (isset($qosOptions['prefetch_count'])) {
            $channel->setPrefetchCount($qosOptions['prefetch_count']);
        }

        //ensure that exchange exists
        $this->createExchange($this->exchangeClass, $channel, $exchangeOptions);

        /** @var \AMQPQueue $queue */
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

        return new $class($queue, $queueOptions);
    }
}
