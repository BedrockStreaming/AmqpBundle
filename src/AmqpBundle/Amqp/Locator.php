<?php

namespace M6Web\Bundle\AmqpBundle\Amqp;

class Locator
{
    /**  @var Consumer[] */
    protected array $consumers = [];

    /** @var Producer[] */
    protected array $producers = [];

    public function getConsumer(string $id): Consumer
    {
        return $this->consumers[$id];
    }

    /** @param Consumer[] $consumers */
    public function setConsumers(array $consumers)
    {
        $this->consumers = $consumers;
    }

    public function getProducer(string $id): Producer
    {
        return $this->producers[$id];
    }

    /** @param Producer[] $producers */
    public function setProducers(array $producers)
    {
        $this->producers = $producers;
    }
}
