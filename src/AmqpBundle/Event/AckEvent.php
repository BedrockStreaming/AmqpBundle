<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Acknowledged message event.
 */
class AckEvent extends Event
{
    public const NAME = 'amqp.ack';

    public function __construct(private readonly int $deliveryTag, private readonly int $flags)
    {
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
