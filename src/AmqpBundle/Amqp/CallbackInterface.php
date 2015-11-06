<?php

namespace M6Web\Bundle\AmqpBundle\Amqp;

/**
 * Callback to be called in Consumer::consume() on message receive
 *
 * @see Consumer
 */
interface CallbackInterface
{
    /**
     * @param \AMQPEnvelope $message
     *
     * @return bool whether message was acked
     */
    public function consume(\AMQPEnvelope $message);
}
