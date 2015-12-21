#!/bin/bash


# credits/original version of this provisionning script : https://github.com/pdezwart/php-amqp/blob/master/provision/install_rabbitmq-c.sh

set -e

echo Installing rabbitmq-c ...


LIBRABBITMQ_VERSION=$1

cd $HOME

git clone git://github.com/alanxz/rabbitmq-c.git
cd $HOME/rabbitmq-c
git checkout ${LIBRABBITMQ_VERSION}

git submodule init && git submodule update
autoreconf -i && ./configure --prefix=$HOME/rabbitmq-c && make && make install