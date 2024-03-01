<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Event;

/**
 * Dispatcher interface.
 */
interface DispatcherInterface
{
    /**
     * Set the sqs command associated with this event.
     *
     * @param string $command The sqs command
     */
    public function setCommand(string $command);

    /**
     * set execution time.
     *
     * @param float $v temps
     */
    public function setExecutionTime(float $v);

    /**
     * set the arguments.
     *
     * @param array $v argus
     */
    public function setArguments(array $v);

    /**
     * set the return value.
     *
     * @param mixed $v value
     */
    public function setReturn(mixed $v);
}
