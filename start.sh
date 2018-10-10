#!/usr/bin/env bash

docker-compose up -d
docker exec -it orderapi_php composer install
docker exec -it orderapi_php php artisan migrate
