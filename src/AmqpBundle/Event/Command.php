<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Command Event.
 */
class Command extends Event implements DispatcherInterface
{
    protected float $executionTime = 0.0;
    protected string $command;
    protected array $arguments;

    protected mixed $return;

    /**
     * {@inheritDoc}
     */
    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get the command associated with this event.
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Set the arguments.
     */
    public function setArguments(array $v): self
    {
        $this->arguments = $v;

        return $this;
    }

    /**
     * Get the arguments.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * set the return value.
     *
     * @param mixed $v value
     */
    public function setReturn(mixed $v): self
    {
        $this->return = $v;

        return $this;
    }

    /**
     * get the return value.
     */
    public function getReturn(): mixed
    {
        return $this->return;
    }

    /**
     * {@inheritDoc}
     */
    public function setExecutionTime(float $v): self
    {
        $this->executionTime = $v;

        return $this;
    }

    /**
     * return the exec time.
     *
     * @return float $v temps
     */
    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    /**
     * Alias of getExecutionTime for the statsd bundle
     * In ms.
     */
    public function getTiming(): float
    {
        return $this->getExecutionTime() * 1000;
    }
}
