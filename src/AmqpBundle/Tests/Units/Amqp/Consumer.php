<?php

namespace M6Web\Bundle\AmqpBundle\Tests\Units\Amqp;

use atoum;
use M6Web\Bundle\AmqpBundle\Amqp\Consumer as Base;
use M6Web\Bundle\AmqpBundle\Sandbox\NullChannel;
use M6Web\Bundle\AmqpBundle\Sandbox\NullConnection;
use M6Web\Bundle\AmqpBundle\Sandbox\NullEnvelope;
use M6Web\Bundle\AmqpBundle\Sandbox\NullQueue;

/**
 * Consumer.
 */
class Consumer extends atoum
{
    public function testConstruct()
    {
        $this
            ->if($queue = $this->getQueue())
            ->if($queueOptions = [])
            ->and($consumer = new Base($queue, $queueOptions))
                ->object($consumer->getQueue())
                    ->isIdenticalTo($queue);
    }

    public function testGetMessageAutoAck()
    {
        $this
            ->if($msgList = ['wait' => ['m1' => 'message1', 'm2' => 'message2']])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // First message : auto ack
                ->object($message = $consumer->getMessage())
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->string($message->getDeliveryTag())
                    ->isEqualTo('m1')
                ->array($msgList['wait'])
                    ->isEqualTo(['m2' => 'message2'])
                ->array($msgList['ack'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['unack'])
                    ->isEmpty()

                // Second message : auto ack
                ->object($message = $consumer->getMessage())
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message2')
                ->string($message->getDeliveryTag())
                    ->isEqualTo('m2')
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['ack'])
                    ->isEqualTo(['m1' => 'message1', 'm2' => 'message2'])
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

    public function testGetMessageManualAck()
    {
        $this
            ->if($msgList = ['wait' => ['m1' => 'message1']])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // First message : manual ack
                ->object($message = $consumer->getMessage(AMQP_NOPARAM))
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->string($message->getDeliveryTag())
                    ->isEqualTo('m1')

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Ack (unknown delivery id)
                ->boolean($message = $consumer->ackMessage(12345))
                    ->isFalse()
                ->mock($queue)
                    ->call('ack')
                        ->withArguments(12345)
                        ->once()

                // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Ack (known delivery id)
                ->boolean($message = $consumer->ackMessage('m1'))
                    ->isTrue()
                ->mock($queue)
                    ->call('ack')
                        ->withArguments('m1')
                        ->once()

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['ack'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['unack'])
                    ->isEmpty()

                // Ack (old delivery id)
                ->boolean($message = $consumer->ackMessage('m1'))
                    ->isFalse();
    }

    public function testGetMessageManualNack()
    {
        $this
            ->if($msgList = ['wait' => ['m1' => 'message1']])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // First message : manual ack
                ->object($message = $consumer->getMessage(AMQP_NOPARAM))
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->string($message->getDeliveryTag())
                    ->isEqualTo('m1')

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Nack (unknown delivery id)
                ->boolean($message = $consumer->nackMessage(12345))
                    ->isFalse()
                ->mock($queue)
                    ->call('nack')
                        ->withArguments(12345)
                        ->once()

                // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                // Nack and requeue (known delivery id)
                ->boolean($message = $consumer->nackMessage('m1', AMQP_REQUEUE))
                    ->isTrue()
                ->mock($queue)
                    ->call('nack')
                        ->withArguments('m1')
                        ->once()

                 // Check data
                ->array($msgList['wait'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEmpty()

                // message : manual ack
                ->object($message = $consumer->getMessage(AMQP_NOPARAM))
                    ->isInstanceOf('\AMQPEnvelope')
                ->string($message->getBody())
                    ->isEqualTo('message1')
                ->string($message->getDeliveryTag())
                    ->isEqualTo('m1')

                 // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEqualTo(['m1' => 'message1'])
                ->array($msgList['ack'])
                    ->isEmpty()

                 // Nack and forget (known delivery id)
                ->boolean($message = $consumer->nackMessage('m1'))
                    ->isTrue()

                // Check data
                ->array($msgList['wait'])
                    ->isEmpty()
                ->array($msgList['ack'])
                    ->isEmpty()
                ->array($msgList['unack'])
                    ->isEmpty()

                // Nack (old delivery id)
                ->boolean($message = $consumer->nackMessage('m1'))
                    ->isFalse();
    }

    public function testPurge()
    {
        $this
            ->if($msgList = ['wait' => ['m1' => 'message1']])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // Purge the queue
                ->boolean($consumer->purge())
                    ->isTrue()
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

    public function testGetMessageCurrentCount()
    {
        $this
            ->if($msgList = [
                'wait' => ['m1' => 'message1', 'm2' => 'message2'],
                'flags' => AMQP_DURABLE | AMQP_EXCLUSIVE | AMQP_AUTODELETE,
            ])
            ->and($queue = $this->getQueue($msgList))
            ->and($consumer = new Base($queue, []))
                // Declare queue in passive mode
                ->integer($consumer->getCurrentMessageCount())
                    ->isEqualTo(count($msgList['wait']))
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

    public function testConsumerWithNullQueue()
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
            // Simulate a simple queue
            reset($msgList['wait']);
            $key = key($msgList['wait']);
            $msg = array_shift($msgList['wait']);

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

            $message->getMockController()->getBody = function () use ($key, $msg) {
                return $msg;
            };

            $message->getMockController()->getDeliveryTag = function () use ($key, $msg) {
                return $key;
            };

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
            return count($msgList['wait']);
        };

        return $queue;
    }
}
