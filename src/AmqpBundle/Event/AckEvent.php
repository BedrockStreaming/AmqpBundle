<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Acknowledged message event.
 */
class AckEvent extends Event
{
    const NAME = 'amqp.ack';

    private string $deliveryTag;

    private int $flags;

    public function __construct(string $deliveryTag, int $flags)
    {
        $this->deliveryTag = $deliveryTag;
        $this->flags = $flags;
    }

    public function getDeliveryTag(): string
    {
        return $this->deliveryTag;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }
}
