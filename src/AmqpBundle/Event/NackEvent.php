<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Not acknowledged message event.
 */
class NackEvent extends Event
{
    const NAME = 'amqp.nack';

    private int $deliveryTag;
    private int $flags;

    public function __construct(int $deliveryTag, int $flags)
    {
        $this->deliveryTag = $deliveryTag;
        $this->flags = $flags;
    }

    public function getDeliveryTag(): int
    {
        return $this->deliveryTag;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }
}
