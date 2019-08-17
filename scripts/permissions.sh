#!/usr/bin/env bash

docker-compose exec php chown -R www-data:www-data var/
docker-compose exec php chmod -R 777 var/