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
     * @param string $name
     *
     * Construct the data collector
     */
    public function __construct(string $name)
    {
        $this->data['name'] = $name;
        $this->reset();
    }

    /**
     * Collect the data.
     *
     * @param Request    $request   The request object
     * @param Response   $response  The response object
     * @param \Exception $exception An exception
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
    }

    /**
     * Listen for command event.
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
    public function getCommands(): array
    {
        return $this->data['commands'];
    }

    /**
     * Return the name of the collector.
     *
     * @return string data collector name
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Return total command execution time.
     *
     * @return float
     */
    public function getTotalExecutionTime(): float
    {
        return (float) array_sum(array_column($this->data['commands'], 'executiontime'));
    }

    /**
     * Get average execution time.
     *
     * @return float
     */
    public function getAvgExecutionTime(): float
    {
        return $this->getTotalExecutionTime() ? ($this->getTotalExecutionTime() / \count($this->data['commands'])) : (float) 0;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // Reset the current data, while keeping the 'name' intact.
        $this->data = [
            'name' => $this->data['name'],
            'commands' => [],
        ];
    }
}
