<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Amqp;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract AMQP.
 */
abstract class AbstractAmqp
{
    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * Class of the event notifier.
     *
     * @var ?class-string
     */
    protected ?string $eventClass = null;

    /**
     * Notify an event to the event dispatcher.
     *
     * @param string $command   The command name
     * @param array  $arguments Args of the command
     * @param mixed  $return    Return value of the command
     * @param float  $time      Exec time
     */
    protected function notifyEvent(string $command, array $arguments, mixed $return, float $time = 0): void
    {
        if ($this->eventDispatcher) {
            $event = new $this->eventClass();
            $event->setCommand($command)
                  ->setArguments($arguments)
                  ->setReturn($return)
                  ->setExecutionTime($time);

            $this->eventDispatcher->dispatch($event, 'amqp.command');
        }
    }

    /**
     * Call a method and notify an event.
     *
     * @param object $object    Method object
     * @param string $name      Method name
     * @param array  $arguments Method arguments
     */
    protected function call(object $object, string $name, array $arguments = [])
    {
        $start = microtime(true);

        $ret = \call_user_func_array([$object, $name], $arguments);

        $this->notifyEvent($name, $arguments, $ret, microtime(true) - $start);

        return $ret;
    }

    /**
     * Set an event dispatcher to notify amqp command.
     *
     * @param EventDispatcherInterface $eventDispatcher The eventDispatcher object, which implement the notify method
     * @param string                   $eventClass      The event class used to create an event and send it to the event dispatcher
     *
     * @throws \Exception
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher, string $eventClass): void
    {
        $class = new \ReflectionClass($eventClass);
        if (!$class->implementsInterface(\M6Web\Bundle\AmqpBundle\Event\DispatcherInterface::class)) {
            throw new Exception('The Event class : '.$eventClass.' must implement DispatcherInterface');
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->eventClass = $eventClass;
    }
}
