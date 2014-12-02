# AmQPBundle

The configuration and documentation is inspired from [videlalvaro/RabbitMqBundle](https://github.com/videlalvaro/RabbitMqBundle).

#### Amqp client as a Symfony Service

The AmqpBundle incorporates messaging in your application using the [php-amqp extension](http://pecl.php.net/package/amqp).
It can communicate with any AMQP spec 0-9-1 compatible server, such as RabbitMQ, OpenAMQP and Qpid,
giving you the ability to publish to any exchange and consume from any queue.

Publishing messages to AMQP Server from a Symfony2 controller is as easy as:

```php
$msg = ["key" => "value"];
$this->get('m6web_amqp.producer.myproducer')->publishMessage(serialize($msg));
```

When you want to consume a message out of a queue :

```php
$msg = $this->get('m6web_amqp.consumer.myconsumer')->getMessage();
```

For now, the AmQPBundle do not provide a daemon mode to run AMQP consumers but you can do it with the [M6Web/DaemonBundle](https://github.com/M6Web/DaemonBundle).

## Installation ##

### For Symfony >= 2.1.* ###

Require the bundle in your composer.json file:

````
{
    "require": {
        "m6web/amqp-bundle": "0.*",
    }
}
```

Register the bundle:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new M6Web\Bundle\AmqpBundle\M6WebAmqpBundle(),
    );
}
```

Install the bundle:

```
$ composer update m6web/amqp-bundle
```

## Usage ##

Add the `m6_web_amqp` section in your configuration file:

```yaml
m6_web_amqp:
    disable_data_collector: true
    connections:
        default:
            host:     'localhost'  # optional - default 'localhost'
            port:     5672         # optional - default 5672
            timeout:  10           # optional - default 10
            login:     'guest'     # optional - default 'guest'
            password: 'guest'      # optional - default 'guest'
            vhost:    '/'          # optional - default '/'
    producers:
        myproducer:
            class: "My\\Provider\\Class"                           # optional - to overload the default provider class
            connection: myconnection                               # require
            exchange_options:
                name: 'myexchange'                                 # require
                type: direct/fanout/headers/topic                  # require
                passive: bool                                      # optional - defaut false
                durable: bool                                      # optional - defaut true
                auto_delete: bool                                  # optional - defaut false
                arguments: { key: value }                          # optional - default { } - Please refer to the documentation of your broker for information about the arguments.
                routing_keys: { 'routingkey', 'routingkey2' }      # optional - default { }
                publish_attributes: { key: value }                 # optional - default { } - possible keys : content_type, content_encoding, message_id, user_id, app_id, delivery_mode,
                                                                   #                          priority, timestamp, expiration, type, reply_to, headers.

    consumers:
        myconsumer:
            class: "My\\Provider\\Class"                      # optional - to overload the default consumer class
            connection: default
            exchange_options:
                name: 'myexchange'                            # require
            queue_options:
                name: 'myqueue'                               # require
                passive: bool                                 # optional - defaut false
                durable: bool                                 # optional - defaut true
                exclusive: bool                               # optional - defaut false
                auto_delete: bool                             # optional - defaut false
                arguments: { key: value }                     # optional - default { } - Please refer to the documentation of your broker for information about the arguments.
                                                              #                          RabbitMQ ex : {'x-ha-policy': 'all', 'x-dead-letter-routing-key': 'async.dead',
                                                              #                                      'x-dead-letter-exchange': 'async_dead', 'x-message-ttl': 20000}
                routing_keys: { 'routingkey', 'routingkey2' } # optional - default { }
```

Here we configure the connection service and the message endpoints that our application will have.

In this example your service container will contain the service `m6web_amqp.producer.myproducer` and `m6web_amqp.consumer.myconsumer`.

### Producer

A producer will be used to send messages to the server.

In the AMQP Model, messages are sent to an __exchange__, this means that in the configuration for a producer
you will have to specify the connection options along with the exchange options.

Let's say that you want to publish a message :

```php
$msg = ["key" => "value"];
$this->get('m6web_amqp.producer.myproducer')->publishMessage(serialize($msg));
```

For a producer called __myproducer__, you will have in the service container a service called __m6web\_amqp.producer.myproducer__.

If you need to add option default publish attributes for each message, publish_attributes options can be something like this :

```yaml
publish_attributes: { 'content_type' : 'application/json', 'delivery_mode': 'persistent', 'priority': 8,  'expiration': '3200'}
```

### Consumer

A consumer will be used to get a message from the queue.

Let's say that you want to get a message :

```php
$msg = $this->get('m6web_amqp.consumer.myconsumer')->getMessage();
```

The consumer do not wait for a message : getMessage will return null immediately if no message is available or return a AMQPEnvelope object if a message can be consumed.
The "flags" argument of getMessage accepts MQP_AUTOACK (auto acknowledge by default) or AMQP_NOPARAM (manual acknowledge).

To manually acknowledge a message, use the consumer's ackMessage/nackMessage methods with a delivery_tag argument's value from the AMQPEnvelope object. 
If you choose to not acknowledge the message, the second parameter of nackMessage accepts AMQP_REQUEUE to requeue the message or AMQP_NOPARAM to forget it. 

### DataCollector

DataCollector is enabled by defaut.

To disable :

```
m6_web_aws:
    disable_data_collector: true
```

# Unit tests :

```
    composer install
    ./bin/atoum
```
