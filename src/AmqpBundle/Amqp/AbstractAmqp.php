<?php

namespace M6Web\Bundle\AmqpBundle\Amqp;

use M6Web\Bundle\AmqpBundle\Event\Command;

/**
 * Abstract AMQP
 */
abstract class AbstractAmqp
{
    /**
     * Event dispatcher
     *
     * @var Object
     */
    protected $eventDispatcher = null;

    /**
     * Class of the event notifier
     *
     * @var string
     */
    protected $eventClass = null;

    /**
     * Notify an event to the event dispatcher
     *
     * @param string $command   The command name
     * @param array  $arguments Args of the command
     * @param mixed  $return    Return value of the command
     * @param int    $time      Exec time
     *
     * @return void
     */
    protected function notifyEvent($command, $arguments, $return, $time = 0)
    {
        if ($this->eventDispatcher) {
            $event = new $this->eventClass();
            $event->setCommand($command)
                  ->setArguments($arguments)
                  ->setReturn($return)
                  ->setExecutionTime($time);

            $this->eventDispatcher->dispatch('amqp.command', $event);
        }
    }

    /**
     * Call a method and notify an event
     *
     * @param object $object    Method object
     * @param string $name      Method name
     * @param array  $arguments Method arguments
     *
     * @return mixed
     */
    protected function call($object, $name, array $arguments = [])
    {
        $start = microtime(true);

        $ret = call_user_func_array(array($object, $name), $arguments);

        $this->notifyEvent($name, $arguments, $ret, microtime(true) - $start);

        return $ret;
    }

    /**
     * Set an event dispatcher to notify amqp command
     *
     * @param Object $eventDispatcher The eventDispatcher object, which implement the notify method
     * @param string $eventClass      The event class used to create an event and send it to the event dispatcher
     *
     * @return void
     */
    public function setEventDispatcher($eventDispatcher, $eventClass)
    {
        if (!is_object($eventDispatcher) || !method_exists($eventDispatcher, 'dispatch')) {
            throw new Exception("The EventDispatcher must be an object and implement a dispatch method");
        }

        $class = new \ReflectionClass($eventClass);
        if (!$class->implementsInterface('\M6Web\Bundle\AmqpBundle\Event\DispatcherInterface')) {
            throw new Exception("The Event class : ".$eventClass." must implement DispatcherInterface");
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->eventClass = $eventClass;
    }
}