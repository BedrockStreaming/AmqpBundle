<?php

namespace M6Web\Bundle\AmqpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event as SymfonyEvent;

/**
 * Command Event.
 */
class Command extends SymfonyEvent implements DispatcherInterface
{
    /**
     * @var int
     */
    protected $executionTime = 0;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var mixed
     */
    protected $return;

    /**
     * Set the command associated with this event.
     *
     * @param string $command The command
     *
     * @return $this
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get the command associated with this event.
     *
     * @return string the command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * set the arguments.
     *
     * @param array $v argus
     *
     * @return $this
     */
    public function setArguments($v)
    {
        $this->arguments = $v;

        return $this;
    }

    /**
     * get the arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * set the return value.
     *
     * @param mixed $v value
     *
     * @return $this
     */
    public function setReturn($v)
    {
        $this->return = $v;

        return $this;
    }

    /**
     * get the return value.
     *
     * @return mixed
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * set the exec time.
     *
     * @param float $v temps
     *
     * @return $this
     */
    public function setExecutionTime($v)
    {
        $this->executionTime = $v;

        return $this;
    }

    /**
     * return the exec time.
     *
     * @return float $v temps
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * Alias of getExecutionTime for the statsd bundle
     * In ms.
     *
     * @return float
     */
    public function getTiming()
    {
        return $this->getExecutionTime() * 1000;
    }
}
