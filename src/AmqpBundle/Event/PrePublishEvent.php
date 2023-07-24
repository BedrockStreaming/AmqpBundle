<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * PrePublishEvent.
 */
class PrePublishEvent extends Event
{
    const NAME = 'amqp.pre_publish';

    private string $message;
    private array $routingKeys;
    private int $flags;
    private array $attributes;
    private bool $canPublish;

    public function __construct(string $message, array $routingKeys, int $flags, array $attributes)
    {
        $this->message = $message;
        $this->routingKeys = $routingKeys;
        $this->flags = $flags;
        $this->attributes = $attributes;
        $this->canPublish = true;
    }

    public function canPublish(): bool
    {
        return $this->canPublish;
    }

    public function allowPublish()
    {
        $this->canPublish = true;
    }

    public function denyPublish()
    {
        $this->canPublish = false;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function getRoutingKeys(): array
    {
        return $this->routingKeys;
    }

    public function setRoutingKeys(array $routingKeys)
    {
        $this->routingKeys = $routingKeys;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFlags(int $flags)
    {
        $this->flags = $flags;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }
}
