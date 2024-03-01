<?php

namespace M6Web\Bundle\AmqpBundle\Sandbox;

/**
 * Envelope being created from array.
 */
class NullEnvelope extends \AMQPEnvelope
{
    private string $body;
    private string $routingKey;
    private int $deliveryTag;
    private int $deliveryMode;
    private bool $redelivery;
    private string $contentType;
    private string $contentEncoding;
    private string $type;
    private string $timestamp;
    private int $priority;
    private string $expiration;
    private string $userId;
    private string $appId;
    private string $messageId;
    private string $replyTo;
    private string $correlationId;
    private array $headers;

    /**
     * {@inheritdoc}
     */
    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    public function setRoutingKey(string $routingKey): void
    {
        $this->routingKey = $routingKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryTag(): int
    {
        return $this->deliveryTag;
    }

    public function setDeliveryTag(int $deliveryTag): void
    {
        $this->deliveryTag = $deliveryTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryMode(): int
    {
        return $this->deliveryMode;
    }

    public function setDeliveryMode(int $deliveryMode): void
    {
        $this->deliveryMode = $deliveryMode;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedelivery(): bool
    {
        return $this->redelivery;
    }

    public function setRedelivery(bool $redelivery): void
    {
        $this->redelivery = $redelivery;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentEncoding(): string
    {
        return $this->contentEncoding;
    }

    public function setContentEncoding(string $contentEncoding): void
    {
        $this->contentEncoding = $contentEncoding;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiration(): string
    {
        return $this->expiration;
    }

    public function setExpiration(string $expiration): void
    {
        $this->expiration = $expiration;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function setReplyTo(string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function setCorrelationId(string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(string $headerName): ?string
    {
        return $this->headers[$headerName] ?? null;
    }
}
