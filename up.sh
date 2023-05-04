#!/bin/sh

cd $(dirname $0)

# sudo rm -Rf var
sudo mkdir -p var/lib/mysql var/log/server var/log/client
sudo chown 999:999 var/lib/mysql
sudo chown 33:33 var/log/server var/log/client

if [ ! -f docker-compose.yaml ]
then
	cp docker-compose.yaml.example docker-compose.yaml
fi

docker-compose up -d --build

while ! docker-compose exec database netstat -nl | grep 3306 > /dev/null
do
	sleep 1
done

docker-compose exec server php bin/console doctrine:migrations:migrate --no-interaction
