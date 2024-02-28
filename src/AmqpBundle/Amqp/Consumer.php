<?php

namespace M6Web\Bundle\AmqpBundle\Amqp;

use M6Web\Bundle\AmqpBundle\Event\AckEvent;
use M6Web\Bundle\AmqpBundle\Event\NackEvent;
use M6Web\Bundle\AmqpBundle\Event\PreRetrieveEvent;
use M6Web\Bundle\AmqpBundle\Event\PurgeEvent;

/**
 * Consumer.
 */
class Consumer extends AbstractAmqp
{

    protected \AMQPQueue $queue;

    protected array $queueOptions = [];

    public function __construct(\AMQPQueue $queue, array $queueOptions)
    {
        $this->queue = $queue;
        $this->queueOptions = $queueOptions;
    }

    /**
     * Retrieve the next message from the queue.
     *
     * @param int $flags MQP_AUTOACK or AMQP_NOPARAM
     *
     * @throws \AMQPConnectionException if the connection to the broker was lost
     * @throws \AMQPChannelException    if the channel is not open
     */
    public function getMessage(int $flags = AMQP_AUTOACK): ?\AMQPEnvelope
    {
        $envelope = $this->call($this->queue, 'get', [$flags]);

        if ($this->eventDispatcher) {
            $preRetrieveEvent = new PreRetrieveEvent($envelope);
            $this->eventDispatcher->dispatch($preRetrieveEvent, PreRetrieveEvent::NAME);

            return $preRetrieveEvent->getEnvelope();
        }

        return $envelope;
    }

    /**
     * Acknowledge the receipt of a message.
     *
     * @param int $deliveryTag delivery tag of last message to ack
     * @param int    $flags       AMQP_MULTIPLE or AMQP_NOPARAM
     *
     * @throws \AMQPChannelException    if the channel is not open
     * @throws \AMQPConnectionException if the connection to the broker was lost
     */
    public function ackMessage(int $deliveryTag, int $flags = AMQP_NOPARAM): void
    {
        if ($this->eventDispatcher) {
            $ackEvent = new AckEvent($deliveryTag, $flags);

            $this->eventDispatcher->dispatch($ackEvent,AckEvent::NAME);
        }

        $this->call($this->queue, 'ack', [$deliveryTag, $flags]);
    }

    /**
     * Mark a message as explicitly not acknowledged.
     *
     * @param int $deliveryTag delivery tag of last message to nack
     * @param int    $flags       AMQP_NOPARAM or AMQP_REQUEUE to requeue the message(s)
     *
     * @throws \AMQPConnectionException if the connection to the broker was lost
     * @throws \AMQPChannelException    if the channel is not open
     */
    public function nackMessage(int $deliveryTag, int $flags = AMQP_NOPARAM): void
    {
        if ($this->eventDispatcher) {
            $nackEvent = new NackEvent($deliveryTag, $flags);

            $this->eventDispatcher->dispatch($nackEvent, NackEvent::NAME);
        }

        $this->call($this->queue, 'nack', [$deliveryTag, $flags]);
    }

    /**
     * Purge the contents of the queue.
     *
     * @throws \AMQPChannelException    if the channel is not open
     * @throws \AMQPConnectionException if the connection to the broker was lost
     */
    public function purge(): int
    {
        if ($this->eventDispatcher) {
            $purgeEvent = new PurgeEvent($this->queue);

            $this->eventDispatcher->dispatch($purgeEvent, PurgeEvent::NAME);
        }

        return $this->call($this->queue, 'purge');
    }

    /**
     * Get the current message count.
     */
    public function getCurrentMessageCount(): int
    {
        // Save the current queue flags and setup the queue in passive mode
        $flags = $this->queue->getFlags();
        $this->queue->setFlags($flags | AMQP_PASSIVE);

        // Declare the queue again as passive to get the count of messages
        $messagesCount = $this->queue->declareQueue();

        // Restore the queue flags
        $this->queue->setFlags($flags);

        return $messagesCount;
    }

    public function getQueue(): \AMQPQueue
    {
        return $this->queue;
    }

    public function setQueue(\AMQPQueue $queue): Consumer
    {
        $this->queue = $queue;

        return $this;
    }
}
