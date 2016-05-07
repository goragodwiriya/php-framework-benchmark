#!/bin/bash

service apache2 start
service mysql start
service ssh start

echo 'You want to run benchmark ? (y/n)'
read answer
if echo "$answer" | grep -iq "^y" ;then
	sh /var/www/html/php-framework-benchmark/benchmark.sh
fi

/bin/bash
