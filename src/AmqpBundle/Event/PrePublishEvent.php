<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * PrePublishEvent.
 */
class PrePublishEvent extends Event
{
    public const NAME = 'amqp.pre_publish';
    private bool $canPublish;

    public function __construct(private string $message, private array $routingKeys, private int $flags, private array $attributes)
    {
        $this->canPublish = true;
    }

    public function canPublish(): bool
    {
        return $this->canPublish;
    }

    public function allowPublish(): void
    {
        $this->canPublish = true;
    }

    public function denyPublish(): void
    {
        $this->canPublish = false;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getRoutingKeys(): array
    {
        return $this->routingKeys;
    }

    public function setRoutingKeys(array $routingKeys): void
    {
        $this->routingKeys = $routingKeys;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
