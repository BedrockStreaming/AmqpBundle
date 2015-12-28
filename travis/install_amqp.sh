#!/bin/sh

bash travis/install_rabbitmq-c.sh v0.7.0



echo Installing extension ...

cd $HOME
git clone git://github.com/pdezwart/php-amqp.git
cd $HOME/php-amqp
git checkout v1.7.0alpha2
phpize
./configure --with-librabbitmq-dir=/home/travis/rabbitmq-c
make && make install
echo "extension = amqp.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini