<?php

namespace M6Web\Bundle\AmqpBundle\Amqp;

use Symfony\Component\HttpKernel\DataCollector\DataCollector as SymfonyDataCollector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handle datacollector for amqp.
 */
class DataCollector extends SymfonyDataCollector
{
    /**
     * @var array
     */
    protected $commands;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     *
     * Construct the data collector
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->data['commands'] = array();
    }

    /**
     * Collect the data.
     *
     * @param Request    $request   The request object
     * @param Response   $response  The response object
     * @param \Exception $exception An exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * Listen for aws command event.
     *
     * @param object $event The event object
     */
    public function onCommand($event)
    {
        $this->data['commands'][] = array(
            'command' => $event->getCommand(),
            'arguments' => $event->getArguments(),
            'executiontime' => $event->getExecutionTime(),
        );
    }

    /**
     * Return command list and number of times they were called.
     *
     * @return array The command list and number of times called
     */
    public function getCommands()
    {
        return $this->data['commands'];
    }

    /**
     * Return the name of the collector.
     *
     * @return string data collector name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Temps total d'exec des commandes.
     *
     * @return float
     */
    public function getTotalExecutionTime()
    {
        $ret = 0;
        foreach ($this->data['commands'] as $command) {
            $ret += $command['executiontime'];
        }

        return $ret;
    }

    /**
     * Temps moyen d'exec.
     *
     * @return float
     */
    public function getAvgExecutionTime()
    {
        return ($this->getTotalExecutionTime()) ? ($this->getTotalExecutionTime() / count($this->data['commands'])) : 0;
    }
}
