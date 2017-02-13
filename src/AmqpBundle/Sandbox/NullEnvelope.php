<?php

namespace M6Web\Bundle\AmqpBundle\Sandbox;

/**
 * Envelope being created from array.
 */
class NullEnvelope extends \AMQPEnvelope
{
    /**
     * @var string
     */
    private $body;
    /**
     * @var string
     */
    private $routingKey;
    /**
     * @var string
     */
    private $deliveryTag;
    /**
     * @var string
     */
    private $deliveryMode;
    /**
     * @var bool
     */
    private $redelivery;
    /**
     * @var string
     */
    private $contentType;
    /**
     * @var string
     */
    private $contentEncoding;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $timestamp;
    /**
     * @var int
     */
    private $priority;
    /**
     * @var string
     */
    private $expiration;
    /**
     * @var string
     */
    private $userId;
    /**
     * @var string
     */
    private $appId;
    /**
     * @var string
     */
    private $messageId;
    /**
     * @var string
     */
    private $replyTo;
    /**
     * @var string
     */
    private $correlationId;
    /**
     * @var array
     */
    private $headers;

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * @param string $routingKey
     */
    public function setRoutingKey($routingKey)
    {
        $this->routingKey = $routingKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryTag()
    {
        return $this->deliveryTag;
    }

    /**
     * @param string $deliveryTag
     */
    public function setDeliveryTag($deliveryTag)
    {
        $this->deliveryTag = $deliveryTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     * @param string $deliveryMode
     */
    public function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedelivery()
    {
        return $this->redelivery;
    }

    /**
     * @param bool $redelivery
     */
    public function setRedelivery($redelivery)
    {
        $this->redelivery = $redelivery;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * @param string $contentEncoding
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param string $expiration
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param string $replyTo
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getCorrelationId()
    {
        return $this->correlationId;
    }

    /**
     * @param string $correlationId
     */
    public function setCorrelationId($correlationId)
    {
        $this->correlationId = $correlationId;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($headerKey)
    {
        return isset($this->headers[$headerKey]) ? $this->headers[$headerKey] : false;
    }
}
