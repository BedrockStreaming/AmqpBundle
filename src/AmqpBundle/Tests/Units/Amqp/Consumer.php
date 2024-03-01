<?php

declare(strict_types=1);

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Amqp;

use M6Web\Bundle\AmqpBundle\Amqp\Consumer as Base;
use M6Web\Bundle\AmqpBundle\Sandbox\NullChannel;
use M6Web\Bundle\AmqpBundle\Sandbox\NullConnection;
use M6Web\Bundle\AmqpBundle\Sandbox\NullEnvelope;
use M6Web\Bundle\AmqpBundle\Sandbox\NullQueue;

/**
 * Consumer.
 */
class Consumer extends \atoum
{
    public function testConstruct(): void
    {
        $this
            ->if($queue = $this->getQueue())
            ->if($queueOptions = [])
            ->and($consumer = new Base($queue, $queueOptions))
                ->object($consumer->getQueue())
                    ->isIdenticalTo($queue);
    }

    public function testGetMessageAutoAck(): void
    {
        $msgList = ['wait' => [1 => 'message1', '2' => 'message2']];
        $this
            ->if($msgList)
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // First message : auto ack
                ->object($message = $consumer->getMessage())
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->integer($message->getDeliveryTag())
                    ->isEqualTo(1)
                ->array($msgList['wait'])
                    ->isEqualTo([2 => 'message2'])
                ->array($msgList['ack'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['unack'])
                    ->isEmpty()

                // Second message : auto ack
                ->object($message = $consumer->getMessage())
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message2')
                ->integer($message->getDeliveryTag())
                    ->isEqualTo(2)
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['ack'])
                    ->isEqualTo([1 => 'message1', 2 => 'message2'])
                ->array($msgList['unack'])
                    ->isEmpty()

                // Queue empty
                ->variable($message = $consumer->getMessage())
                    ->isNull()

                ->mock($queue)
                    ->call('get')
                        ->withArguments(AMQP_AUTOACK)
                        ->exactly(3);
    }

    public function testGetMessageManualAck(): void
    {
        $this
            ->if($msgList = ['wait' => [1 => 'message1']])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // First message : manual ack
                ->object($message = $consumer->getMessage(AMQP_NOPARAM))
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->integer($message->getDeliveryTag())
                    ->isEqualTo(1)

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Ack (unknown delivery id)
                ->and($message = $consumer->ackMessage(12345))
                  ->variable($message)
                  ->isNull()
                  ->mock($queue)
                      ->call('ack')
                          ->withArguments(12345)
                          ->once()

                // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Ack (known delivery id)
                ->and($message = $consumer->ackMessage(1))
                  ->variable($message)
                  ->isNull()
                  ->mock($queue)
                      ->call('ack')
                          ->withArguments(1)
                          ->once()

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['ack'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['unack'])
                    ->isEmpty();
    }

    public function testGetMessageManualNack(): void
    {
        $this
            ->if($msgList = ['wait' => [1 => 'message1']])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // First message : manual ack
                ->object($message = $consumer->getMessage(AMQP_NOPARAM))
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->integer($message->getDeliveryTag())
                    ->isEqualTo(1)

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Nack (unknown delivery id)
                ->and($message = $consumer->nackMessage(12345))
                  ->variable($message)
                  ->isNull()
                  ->mock($queue)
                      ->call('nack')
                          ->withArguments(12345)
                          ->once()

                // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Nack and requeue (known delivery id)
                ->and($message = $consumer->nackMessage(1, AMQP_REQUEUE))
                  ->variable($message)
                  ->isNull()
                  ->mock($queue)
                      ->call('nack')
                          ->withArguments(1)
                          ->once()

                 // Check data
                ->array($msgList['wait'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEmpty()

                // message : manual ack
                ->object($message = $consumer->getMessage(AMQP_NOPARAM))
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->integer($message->getDeliveryTag())
                    ->isEqualTo(1)

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo([1 => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                 // Nack and forget (known delivery id)
                ->and($message = $consumer->nackMessage(1))
                    ->variable($message)
                    ->isNull()

                // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['ack'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEmpty()

                // Nack (old delivery id)
                ->variable($message = $consumer->nackMessage(1))
                    ->isEqualTo(null);
    }

    public function testPurge(): void
    {
        $this
            ->if($msgList = ['wait' => [1 => 'message1']])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // Purge the queue
                ->integer($consumer->purge())
                    ->isEqualTo(1)
                ->mock($queue)
                    ->call('purge')
                        ->once()

                // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['ack'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEmpty()

                // Queue empty
                ->variable($consumer->getMessage())
                    ->isNull();
    }

    public function testGetMessageCurrentCount(): void
    {
        $this
            ->if($msgList = [
                'wait' => [1 => 'message1', 2 => 'message2'],
                'flags' => AMQP_DURABLE | AMQP_EXCLUSIVE | AMQP_AUTODELETE,
            ])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // Declare queue in passive mode
                ->integer($consumer->getCurrentMessageCount())
                    ->isEqualTo(\count($msgList['wait']))
                ->mock($queue)
                    ->call('getFlags')
                        ->once()
                     ->call('setFlags')
                        ->withArguments($msgList['flags'] | AMQP_PASSIVE)
                        ->once()
                    ->call('declareQueue')
                        ->once()
                    ->call('setFlags')
                        ->withArguments($msgList['flags'])
                        ->once()

                ->integer($queue->getFlags())
                    ->isEqualTo($msgList['flags']);
    }

    public function testConsumerWithNullQueue(): void
    {
        $this
            ->if($connection = new NullConnection())
                ->and($channel = new NullChannel($connection))
                ->and($queue = new NullQueue($channel))
                ->and($consumer = new Base($queue, []))
            ->then
                ->variable($consumer->getMessage())->isNull()
                ->integer($consumer->getCurrentMessageCount())->isEqualTo(0)
        ;

        $this
            ->if($envelope = new NullEnvelope())
                ->and($queue->enqueue($envelope))
            ->then
                ->integer($consumer->getCurrentMessageCount())->isEqualTo(1)
                ->object($consumer->getMessage())->isEqualTo($envelope)
        ;
    }

    protected function getQueue(&$msgList = [])
    {
        if (!isset($msgList['wait'])) {
            $msgList['wait'] = [];
        }
        if (!isset($msgList['ack'])) {
            $msgList['ack'] = [];
        }
        if (!isset($msgList['unack'])) {
            $msgList['unack'] = [];
        }
        if (!isset($msgList['flags'])) {
            $msgList['flags'] = AMQP_NOPARAM;
        }

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $queue = new \mock\AMQPQueue();

        $queue->getMockController()->get = function ($flags = AMQP_AUTOACK) use (&$msgList) {
            $key = array_key_first($msgList['wait']);
            $msg = reset($msgList['wait']);
            unset($msgList['wait'][$key]);

            if (!$key) {
                return null;
            }

            if ($flags & AMQP_AUTOACK) {
                $msgList['ack'][$key] = $msg;
            } else {
                $msgList['unack'][$key] = $msg;
            }

            // Message
            $message = new \mock\AMQPEnvelope();

            $message->getMockController()->getBody = fn () => $msg;

            $message->getMockController()->getDeliveryTag = fn () => $key;

            return $message;
        };

        $queue->getMockController()->ack = function ($delivery_tag) use (&$msgList) {
            if (isset($msgList['unack'][$delivery_tag])) {
                $msgList['ack'][$delivery_tag] = $msgList['unack'][$delivery_tag];
                unset($msgList['unack'][$delivery_tag]);

                return true;
            }

            return false;
        };

        $queue->getMockController()->nack = function ($delivery_tag, $flags = AMQP_NOPARAM) use (&$msgList) {
            if (isset($msgList['unack'][$delivery_tag])) {
                if ($flags & AMQP_REQUEUE) {
                    $msgList['wait'][$delivery_tag] = $msgList['unack'][$delivery_tag];
                }
                unset($msgList['unack'][$delivery_tag]);

                return true;
            }

            return false;
        };

        $queue->getMockController()->purge = function () use (&$msgList) {
            $msgList['wait'] = [];

            return true;
        };

        $queue->getMockController()->setFlags = function ($flags) use (&$msgList) {
            $msgList['flags'] = $flags;

            return $this;
        };

        $queue->getMockController()->getFlags = function () use (&$msgList) {
            return $msgList['flags'];
        };

        $queue->getMockController()->declareQueue = function () use (&$msgList) {
            return \count($msgList['wait']);
        };

        return $queue;
    }
}
