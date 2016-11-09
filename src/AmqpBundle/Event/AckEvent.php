<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * Acknowledged message event
 */
class AckEvent extends SymfonyEvent
{
    const NAME = 'amqp.ack';

    /**
     * @var string
     */
    private $deliveryTag;

    /**
     * @var int
     */
    private $flags;

    /**
     * Constructor.
     *
     * @param string $deliveryTag
     * @param int    $flags
     */
    public function __construct($deliveryTag, $flags)
    {
        $this->deliveryTag = $deliveryTag;
        $this->flags = $flags;
    }

    /**
     * @return string
     */
    public function getDeliveryTag()
    {
        return $this->deliveryTag;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }
}
