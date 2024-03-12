<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Sandbox;

/**
 * Exchange that does not publish anything.
 */
class NullExchange extends \AMQPExchange
{
    /**
     * {@inheritdoc}
     */
    public function publish(
        $message,
        $routingKey = null,
        $flags = AMQP_NOPARAM,
        array $attributes = [],
    ): void {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function declareExchange(): void
    {
    }
}
