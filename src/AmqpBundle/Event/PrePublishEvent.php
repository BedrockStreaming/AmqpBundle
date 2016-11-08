<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * PrePublishEvent
 */
class PrePublishEvent extends SymfonyEvent
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $routingKeys;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var bool
     */
    private $canPublish;

    /**
     * Constructor
     *
     * @param string $message
     * @param array  $routingKeys
     * @param int    $flags
     * @param array  $attributes
     */
    public function __construct($message, $routingKeys, $flags, $attributes)
    {
        $this->message = $message;
        $this->routingKeys = $routingKeys;
        $this->flags = $flags;
        $this->attributes = $attributes;
        $this->canPublish = true;
    }

    /**
     * @return bool
     */
    public function canPublish()
    {
        return $this->canPublish;
    }

    /**
     * Allow publish
     */
    public function allowPublish()
    {
        $this->canPublish = true;
    }

    /**
     * Deny publish
     */
    public function denyPublish()
    {
        $this->canPublish = false;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getRoutingKeys()
    {
        return $this->routingKeys;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
