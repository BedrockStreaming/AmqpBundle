<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Not acknowledged message event.
 */
class NackEvent extends Event
{
    public const NAME = 'amqp.nack';

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
