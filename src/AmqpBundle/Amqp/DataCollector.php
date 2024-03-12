<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Amqp;

use M6Web\Bundle\AmqpBundle\Event\DispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as SymfonyDataCollector;

/**
 * Handle datacollector for amqp.
 */
class DataCollector extends SymfonyDataCollector
{
    public function __construct(string $name)
    {
        $this->data['name'] = $name;
        $this->reset();
    }

    /**
     * Collect the data.
     *
     * @param Request         $request   The request object
     * @param Response        $response  The response object
     * @param \Throwable|null $exception An exception
     */
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
    }

    /**
     * Listen for command event.
     *
     * @param DispatcherInterface $event The event object
     */
    public function onCommand(DispatcherInterface $event): void
    {
        $this->data['commands'][] = ['command' => $event->getCommand(), 'arguments' => $event->getArguments(), 'executiontime' => $event->getExecutionTime()];
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
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Return total command execution time.
     */
    public function getTotalExecutionTime(): float
    {
        return (float) array_sum(array_column($this->data['commands'], 'executiontime'));
    }

    /**
     * Get average execution time.
     */
    public function getAvgExecutionTime(): float
    {
        return $this->getTotalExecutionTime() ? ($this->getTotalExecutionTime() / \count($this->data['commands'])) : (float) 0;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        // Reset the current data, while keeping the 'name' intact.
        $this->data = [
            'name' => $this->data['name'],
            'commands' => [],
        ];
    }
}
