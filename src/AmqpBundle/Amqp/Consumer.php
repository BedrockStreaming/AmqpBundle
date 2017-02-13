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
    /**
     * @var \AMQPQueue
     */
    protected $queue = null;

    /**
     * @var array
     */
    protected $queueOptions = [];

    /**
     * @param \AMQPQueue $queue        Amqp Queue
     * @param array      $queueOptions Queue options
     */
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
     * @throws \AMQPChannelException    if the channel is not open
     * @throws \AMQPConnectionException if the connection to the broker was lost
     *
     * @return \AMQPEnvelope|bool
     */
    public function getMessage($flags = AMQP_AUTOACK)
    {
        $envelope = $this->call($this->queue, 'get', [$flags]);

        $preRetrieveEvent = new PreRetrieveEvent($envelope);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(PreRetrieveEvent::NAME, $preRetrieveEvent);
        }

        return $preRetrieveEvent->getEnvelope();
    }

    /**
     * Acknowledge the receipt of a message.
     *
     * @param string $deliveryTag delivery tag of last message to ack
     * @param int    $flags       AMQP_MULTIPLE or AMQP_NOPARAM
     *
     * @return bool
     *
     * @throws \AMQPChannelException    if the channel is not open
     * @throws \AMQPConnectionException if the connection to the broker was lost
     */
    public function ackMessage($deliveryTag, $flags = AMQP_NOPARAM)
    {
        if ($this->eventDispatcher) {
            $ackEvent = new AckEvent($deliveryTag, $flags);

            $this->eventDispatcher->dispatch(AckEvent::NAME, $ackEvent);
        }

        return $this->call($this->queue, 'ack', [$deliveryTag, $flags]);
    }

    /**
     * Mark a message as explicitly not acknowledged.
     *
     * @param string $deliveryTag delivery tag of last message to nack
     * @param int    $flags       AMQP_NOPARAM or AMQP_REQUEUE to requeue the message(s)
     *
     * @throws \AMQPChannelException    if the channel is not open
     * @throws \AMQPConnectionException if the connection to the broker was lost
     *
     * @return bool
     */
    public function nackMessage($deliveryTag, $flags = AMQP_NOPARAM)
    {
        if ($this->eventDispatcher) {
            $nackEvent = new NackEvent($deliveryTag, $flags);

            $this->eventDispatcher->dispatch(NackEvent::NAME, $nackEvent);
        }

        return $this->call($this->queue, 'nack', [$deliveryTag, $flags]);
    }

    /**
     * Purge the contents of the queue.
     *
     * @throws \AMQPChannelException    if the channel is not open
     * @throws \AMQPConnectionException if the connection to the broker was lost
     *
     * @return bool
     */
    public function purge()
    {
        if ($this->eventDispatcher) {
            $purgeEvent = new PurgeEvent($this->queue);

            $this->eventDispatcher->dispatch(PurgeEvent::NAME, $purgeEvent);
        }

        return $this->call($this->queue, 'purge');
    }

    /**
     * Get the current message count.
     *
     * @return int
     */
    public function getCurrentMessageCount()
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

    /**
     * @return \AMQPQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param \AMQPQueue $queue
     *
     * @return \M6Web\Bundle\AmqpBundle\Amqp\Consumer
     */
    public function setQueue(\AMQPQueue $queue)
    {
        $this->queue = $queue;

        return $this;
    }
}
