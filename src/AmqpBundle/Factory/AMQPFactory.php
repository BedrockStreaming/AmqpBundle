<?php

namespace M6Web\Bundle\AmqpBundle\Factory;

/**
 * Common code for both producer and consumer factories (channel, exchange declaration and binding).
 */
abstract class AMQPFactory
{
    /**
     * Create and declare exchange.
     *
     * @param string       $exchangeClass
     * @param \AMQPChannel $channel
     * @param array        $exchangeOptions
     *
     * @return \AMQPExchange
     */
    protected function createExchange($exchangeClass, $channel, array $exchangeOptions)
    {
        /** @var \AMQPExchange $exchange */
        $exchange = new $exchangeClass($channel);
        $exchange->setName($exchangeOptions['name']);

        // If the type is not specified, the exchange must exist
        if (isset($exchangeOptions['type'])) {
            $exchange->setType($exchangeOptions['type']);
            $exchange->setArguments($exchangeOptions['arguments']);
            $exchange->setFlags(
                ($exchangeOptions['passive'] ? AMQP_PASSIVE : AMQP_NOPARAM) |
                ($exchangeOptions['durable'] ? AMQP_DURABLE : AMQP_NOPARAM) |
                ($exchangeOptions['auto_delete'] ? AMQP_AUTODELETE : AMQP_NOPARAM)
            );
            $exchange->declareExchange();
        }

        return $exchange;
    }
}
