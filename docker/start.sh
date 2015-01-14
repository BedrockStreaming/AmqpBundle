#!/bin/bash

ulimit -n 1024

echo "RabbitMQ Management : http://localhost:15672/"

exec rabbitmq-server $@